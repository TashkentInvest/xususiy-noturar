<?php

namespace App\Http\Controllers;

use App\Exports\AktivsExport;
use App\Models\Aktiv;
use App\Models\District;
use App\Models\File;
use App\Models\Regions;
use App\Models\Street;
use App\Models\SubStreet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AktivController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->roles[0]->name ?? '';
        $districts = District::all();

        $query = Aktiv::query()->with(['street.district', 'user', 'files']);

        if ($userRole === 'Employee') {
            // Employees only see aktivs assigned to their streets
            $query->whereHas('street', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($userRole === 'Manager') {
            // Managers see all aktivs in their own district
            $query->whereHas('street.district', function ($q) use ($user) {
                $q->where('id', $user->district_id);
            });
        }

        // Optional filters from query string
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('district_id')) {
            $query->whereHas('street.district', function ($q) use ($request) {
                $q->where('id', $request->district_id);
            });
        }

        // Statistics
        $noturarBinoCount = $query->clone()->where('building_type', 'AlohidaSavdoDokoni')->count();
        $turarBinoCount = $query->clone()->where('building_type', 'kopQavatliUy')->count();

        $aktivs = $query->deepFilters() // ✅ no error now
            ->orderBy('updated_at', 'desc')
            ->where('is_status_yer_tola', '!=', 1)
            ->paginate(15)
            ->appends($request->query());

        return view('pages.aktiv.index', compact(
            'aktivs',
            'noturarBinoCount',
            'turarBinoCount',
            'districts'
        ));
    }
    public function userTumanlarCounts(Request $request)
    {
        $user_id = $request->input('user_id');
        $district_id = $request->input('district_id');
        $userRole = auth()->user()->roles->first()->name;

        // Only Super Admins and Managers can filter by user_id or district_id
        if ($userRole != 'Super Admin' && $userRole != 'Manager') {
            abort(403, 'Unauthorized access.');
        }

        // Initialize the query builder for Aktivs
        $query = Aktiv::query();

        // Only Super Admins and Managers can filter by user_id
        if ($userRole == 'Super Admin' || $userRole == 'Manager') {
            if ($user_id) {
                // Filter aktivs by the specified user_id
                $query->where('user_id', $user_id);
            }

            // Apply district filter if provided
            if ($district_id) {
                $query->whereHas('user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            }
        } else {
            // If not Super Admin or Manager, show only the logged-in user's aktivs
            $query->where('user_id', auth()->id());
        }

        // Get distinct districts by joining with users and selecting the distinct district_id
        $districts = District::select('districts.id', 'districts.name_uz') // select relevant columns
            ->distinct()
            ->join('users', 'districts.id', '=', 'users.district_id') // join with users table
            ->join('aktivs', 'users.id', '=', 'aktivs.user_id') // join with aktivs table
            ->whereIn('aktivs.id', $query->pluck('id')) // filter the aktivs based on the query
            ->get();

        // Manually count aktivs for each district
        foreach ($districts as $district) {
            $aktivCount = Aktiv::query()
                ->whereHas('user', function ($q) use ($district, $user_id) {
                    // Apply district filter if needed
                    $q->where('district_id', $district->id);

                    // Apply user_id filter if provided
                    if ($user_id) {
                        $q->where('user_id', $user_id);
                    }
                })
                ->count(); // Get the count of aktivs for the current district

            // Add the count to the district object
            $district->aktiv_count = $aktivCount;
        }

        // Return the view with districts data
        return view('pages.aktiv.tuman_counts', compact('districts'));
    }

    public function create()
    {
        $regions = Regions::get();  // Assuming this needs no filtering
        $isSuperAdmin = auth()->id() === 1;  // Check if the user is the Super Admin
        $userDistrictId = auth()->user()->district_id;  // Get the district ID of the authenticated user

        if ($isSuperAdmin) {
            // Super Admin gets to see all Aktivs except their own creations
            $aktivs = Aktiv::with('files')->where('user_id', '!=', auth()->id())->get();
        } else {
            // Regular users see only Aktivs from their district and not created by themselves
            $aktivs = Aktiv::with('files')
                ->join('streets', 'aktivs.street_id', '=', 'streets.id')  // Join the streets table
                ->where('streets.district_id', $userDistrictId)  // Filter by user's district
                ->where('aktivs.user_id', '!=', auth()->id())  // Exclude their own Aktivs
                ->get();
        }

        $defaultImage = 'https://cdn.dribbble.com/users/1651691/screenshots/5336717/404_v2.png';

        // Assign a default image if no files are associated with an Aktiv
        $aktivs->map(function ($aktiv) use ($defaultImage) {
            $aktiv->main_image = $aktiv->files->first() ? asset('storage/' . $aktiv->files->first()->path) : $defaultImage;
            return $aktiv;
        });

        return view('pages.aktiv.create', compact('aktivs', 'regions'));
    }
    /**
     * Store a newly created Aktiv in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'object_name'      => 'nullable',
                'balance_keeper'   => 'nullable',
                'location'         => 'nullable',
                'land_area'        => 'nullable',
                'building_area'    => 'nullable',
                'gas'              => 'nullable',
                'water'            => 'nullable',
                'electricity'      => 'nullable',
                'additional_info'  => 'nullable',
                'geolokatsiya'     => 'nullable',
                'latitude'         => 'nullable',
                'longitude'        => 'nullable',
                'kadastr_raqami'   => 'nullable',
                'files.*'          => 'nullable',
                'files'            => 'nullable', // Enforces at least 4 files
                'sub_street_id'    => 'nullable',
                'street_id'        => 'required',
                'home_number'      => 'nullable',
                'apartment_number' => 'nullable',
                'user_id'          => 'nullable',
                'building_type'    => 'nullable',
                'kadastr_pdf'      => 'nullable',
                'ijara_shartnoma_nusxasi_pdf' => 'nullable',
                'qoshimcha_fayllar_pdf' => 'nullable',
                'document_type'    => 'nullable',
                'reason_not_active' => 'nullable',
                'ready_for_rent'   => 'nullable',
                'rental_agreement_status' => 'nullable',
                'unused_duration'  => 'nullable',
                'provided_assistance' => 'nullable',
                'start_date'       => 'nullable',
                'additional_notes' => 'nullable',
                'working_24_7'     => 'nullable',
                'owner'            => 'nullable',
                'stir'             => 'nullable',
                'object_type'      => 'nullable',
                'tenant_phone_number' => 'nullable',
                'ijara_summa_wanted' => 'nullable',
                'ijara_summa_fakt' => 'nullable',
                'ijaraga_berishga_tayyorligi' => 'nullable',
                'faoliyat_xolati'  => 'nullable',
                'faoliyat_turi'    => 'nullable',
            ]);

            // Start a database transaction
            DB::beginTransaction();

            // Extract data excluding files
            $data = $request->except([
                'files',
                'kadastr_pdf',
                'ijara_shartnoma_nusxasi_pdf',
                'qoshimcha_fayllar_pdf'
            ]);

            // Set user_id from authenticated user
            $data['user_id'] = auth()->id();

            // Convert faoliyat_turi array to JSON if it exists
            if ($request->has('faoliyat_turi') && is_array($request->faoliyat_turi)) {
                $data['faoliyat_turi'] = json_encode($request->faoliyat_turi);
            }

            // Create the Aktiv
            $aktiv = Aktiv::create($data);

            // Log success with Aktiv ID for debugging
            Log::info('Aktiv created with ID: ' . $aktiv->id);

            // Handle file uploads using the newly implemented handleFiles method
            $this->handleFiles($request, $aktiv);

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('aktivs.index')->with('success', 'Актив муваффақиятли яратилди.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            DB::rollBack();
            Log::error('Validation error creating Aktiv: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Other exceptions
            DB::rollBack();
            Log::error('Error creating Aktiv: ' . $e->getMessage() . ' - Line: ' . $e->getLine() . ' - File: ' . $e->getFile());
            return redirect()->back()
                ->with('error', 'Актив яратишда хатолик юз берди. Илтимос, қайта уриниб кўринг.')
                ->withInput();
        }
    }

    /**
     * Handle file uploads for an Aktiv
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aktiv  $aktiv
     * @return void
     */
    private function handleFiles(Request $request, Aktiv $aktiv)
    {
        try {
            // Handle primary image files (required, minimum 4)
            if ($request->hasFile('files')) {
                $fileCount = 0;

                foreach ($request->file('files') as $file) {
                    // Validate file is valid
                    if (!$file->isValid()) {
                        Log::warning('Invalid file uploaded for Aktiv ID: ' . $aktiv->id);
                        continue;
                    }

                    // Generate a unique filename
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                    // Define the storage path
                    $path = 'aktivs/' . $aktiv->id . '/images';

                    // Store the file
                    $filePath = $file->storeAs('public/' . $path, $filename);

                    if (!$filePath) {
                        Log::error('Failed to store file for Aktiv ID: ' . $aktiv->id);
                        continue;
                    }

                    // Create file record with only the fields present in your schema
                    File::create([
                        'aktiv_id' => $aktiv->id,
                        'path' => $path . '/' . $filename
                    ]);

                    $fileCount++;
                }

                Log::info('Successfully uploaded ' . $fileCount . ' files for Aktiv ID: ' . $aktiv->id);
            }

            // Handle kadastr PDF file
            if ($request->hasFile('kadastr_pdf') && $request->file('kadastr_pdf')->isValid()) {
                $file = $request->file('kadastr_pdf');
                $filename = 'kadastr_' . Str::uuid() . '.pdf';
                $path = 'aktivs/' . $aktiv->id . '/documents';

                $filePath = $file->storeAs('public/' . $path, $filename);

                if ($filePath) {
                    $aktiv->update([
                        'kadastr_pdf' => $path . '/' . $filename
                    ]);
                    Log::info('Kadastr PDF uploaded for Aktiv ID: ' . $aktiv->id);
                } else {
                    Log::error('Failed to store kadastr PDF for Aktiv ID: ' . $aktiv->id);
                }
            }

            // Handle ijara shartnoma PDF file
            if ($request->hasFile('ijara_shartnoma_nusxasi_pdf') && $request->file('ijara_shartnoma_nusxasi_pdf')->isValid()) {
                $file = $request->file('ijara_shartnoma_nusxasi_pdf');
                $filename = 'ijara_shartnoma_' . Str::uuid() . '.pdf';
                $path = 'aktivs/' . $aktiv->id . '/documents';

                $filePath = $file->storeAs('public/' . $path, $filename);

                if ($filePath) {
                    $aktiv->update([
                        'ijara_shartnoma_nusxasi_pdf' => $path . '/' . $filename
                    ]);
                    Log::info('Ijara shartnoma PDF uploaded for Aktiv ID: ' . $aktiv->id);
                } else {
                    Log::error('Failed to store ijara shartnoma PDF for Aktiv ID: ' . $aktiv->id);
                }
            }

            // Handle qoshimcha fayllar PDF file
            if ($request->hasFile('qoshimcha_fayllar_pdf') && $request->file('qoshimcha_fayllar_pdf')->isValid()) {
                $file = $request->file('qoshimcha_fayllar_pdf');
                $filename = 'qoshimcha_fayllar_' . Str::uuid() . '.pdf';
                $path = 'aktivs/' . $aktiv->id . '/documents';

                $filePath = $file->storeAs('public/' . $path, $filename);

                if ($filePath) {
                    $aktiv->update([
                        'qoshimcha_fayllar_pdf' => $path . '/' . $filename
                    ]);
                    Log::info('Qoshimcha fayllar PDF uploaded for Aktiv ID: ' . $aktiv->id);
                } else {
                    Log::error('Failed to store qoshimcha fayllar PDF for Aktiv ID: ' . $aktiv->id);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error handling files for Aktiv ID ' . $aktiv->id . ': ' . $e->getMessage());
            throw $e; // Re-throw to be caught by the parent try-catch
        }
    }

    // Removed duplicate handleFiles method to avoid redeclaration error

    public function show(Aktiv $aktiv)
    {
        // Check if the user can view this Aktiv (for authorization)
        // $this->authorizeView($aktiv);

        // Load necessary relationships using eager loading
        $aktiv->load(['subStreet.district.region', 'files:id,aktiv_id,path']);

        $defaultImage = 'https://cdn.dribbble.com/users/1651691/screenshots/5336717/404_v2.png';

        // Add main_image attribute to the current Aktiv
        $aktiv->main_image = $aktiv->files->first() ? asset('storage/' . $aktiv->files->first()->path) : $defaultImage;

        // Retrieve user district ID from the authenticated user's associated street
        $userDistrictId = auth()->user()->district_id;

        if (auth()->id() === 1) {
            // Super Admin can see all aktivs, limit for better performance
            $aktivs = Aktiv::with('files:id,aktiv_id,path')->limit(10)->get();
        } else {
            // Regular users see only aktivs from their district and not created by Super Admin, limit for better performance
            $aktivs = Aktiv::with('files:id,aktiv_id,path')
                ->join('streets', 'aktivs.street_id', '=', 'streets.id')
                ->where('streets.district_id', $userDistrictId)
                ->where('aktivs.user_id', '!=', 1) // Specify the table name for user_id
                ->limit(10)
                ->get();
        }

        // Add main_image attribute to each Aktiv
        $aktivs->map(function ($a) use ($defaultImage) {
            $a->main_image = $a->files->first() ? asset('storage/' . $a->files->first()->path) : $defaultImage;
            return $a;
        });

        return view('pages.aktiv.show', compact('aktiv', 'aktivs'));
    }
    public function edit(Aktiv $aktiv)
    {
        // $this->authorizeView($aktiv); // Check if the user can edit this Aktiv

        try {
            // Eager load relationships
            $aktiv->load('subStreet.district.region');

            // Get regions
            $regions = Regions::all();

            // Safely access subStreet, district, and region
            $districts = optional($aktiv->subStreet->district->region)->districts ?? collect();
            $streets = optional($aktiv->subStreet->district)->streets ?? collect();
            $substreets = optional($aktiv->subStreet->district)->subStreets ?? collect();

            return view('pages.aktiv.edit', compact('aktiv', 'regions', 'districts', 'streets', 'substreets'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading Aktiv data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading Aktiv data. Please try again.');
        }
    }

    public function getDistricts(Request $request)
    {
        $regionId = $request->region_id;
        $districts = District::where('region_id', $regionId)->pluck('name_uz', 'id')->toArray();

        \Log::info('' . count($districts) . '');
        return response()->json($districts);
    }

    public function getStreets(Request $request)
    {
        $districtId = $request->district_id;
        $streets = Street::where('district_id', $districtId)->pluck('name', 'id')->toArray();
        \Log::info('' . count($streets) . '');
        return response()->json($streets);
    }

    public function getSubStreets(Request $request)
    {
        $districtId = $request->input('district_id');
        if ($districtId) {
            $substreets = SubStreet::where('district_id', $districtId)->pluck('name', 'id')->toArray();
            \Log::info('substreets' . count($substreets) . '');
            return response()->json($substreets);
        }
        return response()->json([]);
    }


    public function getObDistricts(Request $request)
    {
        $regionId = $request->region_id;
        $districts = District::where('region_id', $regionId)->pluck('name_uz', 'id')->toArray();

        return response()->json($districts);
    }

    public function getObStreets(Request $request)
    {
        $districtId = $request->district_id;
        $streets = Street::where('district_id', $districtId)->pluck('name', 'id')->toArray();

        return response()->json($streets);
    }

    public function getObSubStreets(Request $request)
    {
        $districtId = $request->input('district_id');
        if ($districtId) {
            $substreets = SubStreet::where('district_id', $districtId)->pluck('name', 'id');
            return response()->json($substreets);
        }
        return response()->json([]);
    }

    public function createStreet(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'street_name' => 'required',
        ]);

        $street = Street::create([
            'district_id' => $request->district_id,
            'name' => $request->street_name,
            'user_id' => auth()->id() ?? null,
            'created_from_outside' => true,
        ]);

        return response()->json(['id' => $street->id, 'name' => $street->name]);
    }

    public function createSubStreet(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'sub_street_name' => 'required',
        ]);

        $subStreet = SubStreet::create([
            'district_id' => $request->district_id,
            'name' => $request->sub_street_name,
            'user_id' => auth()->id() ?? null,
            'created_from_outside' => true,
        ]);

        return response()->json(['id' => $subStreet->id, 'name' => $subStreet->name]);
    }
    public function update(Request $request, Aktiv $aktiv)
    {
        // $this->authorizeView($aktiv); // Check if the user can update this Aktiv

        $request->validate([
            'object_name'      => 'required',
            'balance_keeper'   => 'required',
            'location'         => 'required',
            'land_area'        => 'required|numeric',
            'building_area'    => 'nullable',
            'gas'              => 'required|string',
            'water'            => 'required|string',
            'electricity'      => 'required|string',
            'additional_info'  => 'nullable',
            'geolokatsiya'     => 'required|string',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'kadastr_raqami'   => 'nullable',
            'files.*'          => 'required',
            'sub_street_id'    => 'required',
            'street_id'    => 'required',

            'home_number'          => 'nullable',
            'apartment_number'          => 'nullable',

            'user_id'          => 'nullable',
            'building_type' => 'nullable|in:yer,kopQavatliUy,AlohidaSavdoDokoni',

            'kadastr_pdf'      => 'nullable|file',
            'ijara_shartnoma_nusxasi_pdf' => 'nullable|file',
            'qoshimcha_fayllar_pdf' => 'nullable|file',

            'document_type' => 'nullable',
            'reason_not_active' => 'nullable',
            'ready_for_rent' => 'nullable',
            'rental_agreement_status' => 'nullable',
            'unused_duration' => 'nullable',
            'provided_assistance' => 'nullable',
            'start_date' => 'nullable',
            'additional_notes' => 'nullable',
            'working_24_7' => 'nullable',
            'owner' => 'nullable',
            'stir' => 'nullable',
            'object_type' => 'nullable',
            'tenant_phone_number' => 'nullable',
            'ijara_summa_wanted' => 'nullable',
            'ijara_summa_fakt' => 'nullable',
            'ijaraga_berishga_tayyorligi' => 'nullable',
            'faoliyat_xolati' => 'nullable',
            'faoliyat_turi' => 'nullable|array', // Ensure it's an array


        ]);

        // $totalFiles = $aktiv->files()->count() - count($request->delete_files ?? []) + count($request->file('files') ?? []);
        // if ($totalFiles < 4) {
        //     return back()->withErrors(['files' => 'Камида 4 та файл бўлиши шарт.'])->withInput();
        // }

        if ($request->has('delete_files')) {
            foreach ($request->delete_files as $fileId) {
                $file = $aktiv->files()->find($fileId);
                if ($file) {
                    \Storage::disk('public')->delete($file->path);
                    $file->delete();
                }
            }
        }



        $data = $request->except('files', 'kadastr_pdf', 'ijara_shartnoma_nusxasi_pdf', 'qoshimcha_fayllar_pdf');
        $aktiv->update($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('assets', 'public');
                $aktiv->files()->create([
                    'path' => $path,
                ]);
            }
        }

        if ($request->hasFile('kadastr_pdf')) {
            $kadastrPath = $request->file('kadastr_pdf')->move(public_path('uploads/aktivs'), 'kadastr_' . time() . '.' . $request->file('kadastr_pdf')->getClientOriginalExtension());
            $aktiv->kadastr_pdf = 'uploads/aktivs/' . basename($kadastrPath);
        }

        if ($request->hasFile('ijara_shartnoma_nusxasi_pdf')) {
            $hokimPath = $request->file('ijara_shartnoma_nusxasi_pdf')->move(public_path('uploads/aktivs'), 'hokim_' . time() . '.' . $request->file('ijara_shartnoma_nusxasi_pdf')->getClientOriginalExtension());
            $aktiv->ijara_shartnoma_nusxasi_pdf = 'uploads/aktivs/' . basename($hokimPath);
        }

        if ($request->hasFile('qoshimcha_fayllar_pdf')) {
            $transferPath = $request->file('qoshimcha_fayllar_pdf')->move(public_path('uploads/aktivs'), 'transfer_' . time() . '.' . $request->file('qoshimcha_fayllar_pdf')->getClientOriginalExtension());
            $aktiv->qoshimcha_fayllar_pdf = 'uploads/aktivs/' . basename($transferPath);
        }

        // Save the updated model
        $aktiv->save();


        return redirect()->route('aktivs.index')->with('success', 'Aktiv updated successfully.');
    }
    public function destroy(Aktiv $aktiv)
    {
        $this->authorizeView($aktiv); // Check if the user can delete this Aktiv

        foreach ($aktiv->files as $file) {
            \Storage::disk('public')->delete($file->path);
            $file->delete();
        }

        $aktiv->delete();

        return redirect()->route('aktivs.index')->with('success', 'Aktiv deleted successfully.');
    }

    /**
     * Check if the authenticated user is authorized to view, edit, or delete an Aktiv.
     *
     * @param Aktiv $aktiv
     * @return void
     */
    private function authorizeView(Aktiv $aktiv)
    {
        $userRole = auth()->user()->roles->first()->name;

        // if ($userRole == 'Super Admin') {
        if ($userRole == 'Super Admin' || $userRole == 'Manager') {
            // Super Admins and Managers can access any Aktiv
            return;
        }

        if ($aktiv->user_id == auth()->id()) {
            // The Aktiv belongs to the authenticated user
            return;
        }

        // If none of the above, deny access
        abort(403, 'Unauthorized access.');
    }


    public function kadastrTumanlarCounts(Request $request)
    {
        $user_id = $request->input('user_id');
        $district_id = $request->input('district_id');
        $userRole = auth()->user()->roles->first()->name;

        // Only Super Admins and Managers can filter by user_id or district_id
        if ($userRole != 'Super Admin' && $userRole != 'Manager') {
            abort(403, 'Unauthorized access.');
        }

        // Initialize the query builder for Aktivs
        $query = Aktiv::query();

        // Only Super Admins and Managers can filter by user_id
        if ($userRole == 'Super Admin' || $userRole == 'Manager') {
            if ($user_id) {
                // Filter aktivs by the specified user_id
                $query->where('user_id', $user_id);
            }

            // Apply district filter if provided
            if ($district_id) {
                $query->whereHas('user', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            }
        } else {
            // If not Super Admin or Manager, show only the logged-in user's aktivs
            $query->where('user_id', auth()->id());
        }

        // Get distinct districts by joining with users and selecting the distinct district_id
        $districts = District::select('districts.id', 'districts.name_uz') // select relevant columns
            ->distinct()
            ->join('users', 'districts.id', '=', 'users.district_id') // join with users table
            ->join('aktivs', 'users.id', '=', 'aktivs.user_id') // join with aktivs table
            ->whereIn('aktivs.id', $query->pluck('id')) // filter the aktivs based on the query
            ->get();

        // Manually count aktivs for each district and filter based on kadastr_raqami
        foreach ($districts as $district) {
            $aktivCount = Aktiv::query()
                ->whereHas('user', function ($q) use ($district, $user_id) {
                    // Apply district filter if needed
                    $q->where('district_id', $district->id);

                    // Apply user_id filter if provided
                    if ($user_id) {
                        $q->where('user_id', $user_id);
                    }
                })
                ->whereNotNull('kadastr_raqami')
                ->where('kadastr_raqami', '!=', '')
                ->where('kadastr_raqami', '!=', '00:00:00:00:00:0000')

                ->where('kadastr_raqami', 'not like', '00%') // exclude invalid kadastr_raqami
                ->count(); // Get the count of aktivs with valid kadastr_raqami for the current district

            // Add the count to the district object
            $district->aktiv_count = $aktivCount;
        }

        // Return the view with districts data
        return view('pages.aktiv.kadastr_tuman_counts', compact('districts'));
    }

    public function kadastrBorlar(Request $request)
    {
        // Fetch Aktiv records that have a valid Kadastr number
        $aktiv_kadastr = Aktiv::whereNotNull('kadastr_raqami')
            ->where('kadastr_raqami', '!=', '')
            ->where('kadastr_raqami', '!=', '00:00:00:00:00:0000')
            ->where('kadastr_raqami', 'not like', '00%')
            ->with('user') // Include user data
            ->get();

        // Return to the view with the Kadastr records
        return view('pages.aktiv.kadastr_borlar', compact('aktiv_kadastr'));
    }

    public function kadastrByDistrict($district_id)
    {
        // Fetch the district along with its associated Aktiv records (through Street)
        $district = District::with(['aktives' => function ($query) {
            $query->where('kadastr_raqami', '!=', '')
                ->where('kadastr_raqami', '!=', '00:00:00:00:00:0000')
                ->where('kadastr_raqami', 'not like', '00%')
                ->with('user'); // Include user data
        }])->findOrFail($district_id);

        // Return to the view with the district's Kadastr records
        return view('pages.aktiv.kadastr_by_district', compact('district'));
    }


    public function userAktivCounts(Request $request)
    {
        // Get the user's role and district from the authenticated user
        $userRole = auth()->user()->roles->first()->name;
        $user_id = auth()->user()->id;
        $district_id = auth()->user()->district_id; // Assuming district_id is a property on the user model

        // Only Super Admins and Managers can access this page
        if ($userRole != 'Super Admin' && $userRole != 'Manager') {
            abort(403, 'Unauthorized access.');
        }

        // Initialize the query with the User model
        $query = User::query();

        // If a district_id is provided in the request, use it, otherwise use the authenticated user's district_id
        $requestDistrictId = $request->input('district_id');
        if ($requestDistrictId) {
            // Apply the district filter from the request
            $query->whereHas('user', function ($q) use ($requestDistrictId) {
                $q->where('district_id', $requestDistrictId);
            });
        } else {
            // If no district_id is provided in the request, we should check whether the user is assigned to a district directly
            if ($district_id) {
                // Filter by the district_id of the authenticated user
                $query->where('district_id', $district_id);
                // dd($district_id);
            } else {
                // If the user doesn't have a direct district_id, filter by the district related to the user's aktiv
                $query->whereHas('aktivs', function ($q) {
                    // Check if the associated aktiv has a related street and district
                    $q->whereHas('street', function ($q) {
                        $q->whereHas('district');
                    });
                });
            }
        }

        // Get users with their associated aktiv counts
        $users = $query->withCount('aktivs')->get();



        // Return the view with the users data
        return view('pages.aktiv.user_counts', compact('users'));
    }


    public function export()
    {
        // dd('daw');
        return Excel::download(new AktivsExport, 'aktivs.xlsx');
    }

    public function myMap()
    {
        $userRole = auth()->user()->roles->first()->name;

        if ($userRole == 'Super Admin') {
            return view('pages.aktiv.map_orginal');
        } else {
            abort(403, 'Unauthorized access.');
        }
    }

    // map code with source data


    public function getLots()
    {
        // Check if the authenticated user is the Super Admin (user_id = 1)
        $isSuperAdmin = auth()->id() === 1;

        if ($isSuperAdmin) {
            // Super Admin sees all aktivs
            $aktivs = Aktiv::with(['files', 'user'])->get();
        } else {
            // Other users should not see aktivs created by the Super Admin (user_id = 1)
            $aktivs = Aktiv::with(['files', 'user'])
                // ->where('user_id', '!=', 1)  // Exclude records created by the Super Admin
                ->get();
        }

        // Define the default image in case there is no image
        $defaultImage = 'https://cdn.dribbble.com/users/1651691/screenshots/5336717/404_v2.png';

        // Map the aktivs to the required format
        $lots = $aktivs->map(function ($aktiv) use ($defaultImage) {
            // Determine the main image URL
            $mainImagePath = $aktiv->files->first() ? 'storage/' . $aktiv->files->first()->path : null;
            $mainImageUrl = $mainImagePath && file_exists(public_path($mainImagePath))
                ? asset($mainImagePath)
                : $defaultImage;

            // Return the necessary data
            return [
                'lat' => $aktiv->latitude ?? null,
                'lng' => $aktiv->longitude ?? null,
                'property_name' => $aktiv->object_name ?? null,
                'main_image' => $mainImageUrl ?? null,
                'land_area' => $aktiv->land_area ?? null,
                'start_price' => $aktiv->start_price ?? 0,
                'lot_link' => route('aktivs.show', $aktiv->id ?? null),
                'lot_number' => $aktiv->id ?? null,
                'address' => $aktiv->location ?? null,
                'user_name' => $aktiv->user ? $aktiv->user->name : 'N/A',
                'user_email' => $aktiv->user ? $aktiv->user->email : 'N/A',
                'building_type' => $aktiv->building_type ?? null // Include building_type

            ];
        });

        // Return the response as JSON
        return response()->json(['lots' => $lots]);
    }



    /**
     * Generate a QR code for the given lot's latitude and longitude
     *
     * @param string $lat Latitude of the lot
     * @param string $lng Longitude of the lot
     * @return \Illuminate\Http\Response
     */


    public function kadastr_index(Aktiv $aktiv)
    {
        $this->authorizeView($aktiv);

        // dd('aktivs.kadastr_index');
        return view('pages.aktiv.kadastr_index');
    }

    public function kadastr(Request $request)
    {
        $validated = $request->validate([
            'cadastre_numbers' => 'required|string', // Expect multi-line input
        ]);

        // Process input into an array
        $cadastreNumbers = array_filter(array_map('trim', explode("\n", $validated['cadastre_numbers'])));

        // dump($cadastreNumbers);
        Log::info($cadastreNumbers);
        $results = [];
        foreach ($cadastreNumbers as $number) {
            // dump($number);

            // if (!preg_match('/^\d{2}:\d{2}:\d{2}:\d{2}(:\d+(:\d+)?|\/\d+)?(:\d+(:\d+)?)?$/', $number)) {

            //     $results[] = [
            //         'cad_number' => $number,
            //         'error' => "Invalid format for cadastral number: $number",
            //     ];
            //     continue;
            // }

            try {
                // Make API request

                $response = Http::get("http://otchet.davbaho.uz/api/get_cadastre_second/1?num=" . $number);

                // $response = Http::get("http://otchet.davbaho.uz/api/get_cadastre_second/1", [
                //     Log::info($response),

                //     'num' => $number,
                // ]);


                if ($response->successful()) {
                    $data = $response->json();
                    Log::info($data);

                    // Log::info($data);
                    if (isset($data['documents'])) {
                        Log::info($data['documents']);

                        $documents = $data['documents'];
                    } else {
                        Log::warning("No documents found for cadastral number: $number");
                        $documents = [];
                    }


                    $results[] = [
                        'cad_number' => $data['cad_number'] ?? $number,
                        'region' => $data['region'] ?? 'Unknown',
                        'district' => $data['district'] ?? 'Unknown',
                        'address' => $data['address'] ?? 'Unknown',
                        'land_area' => ($data['land_area'] ?? '0') . ' m²',
                        'bans' => $data['bans'] ?? [],
                        'documents' => $data['documents'] ?? [],
                        'tipText' => $data['tipText'] ?? 'Unknown',
                        'vidText' => $data['vidText'] ?? 'Unknown',
                        'error' => null,
                    ];
                } else {
                    // dd('not');

                    // Handle unsuccessful HTTP responses
                    $results[] = [
                        'cad_number' => $number,
                        'error' => "Failed to fetch data for cadastral number: $number (HTTP {$response->status()})",
                    ];
                }
            } catch (\Exception $e) {
                // dd('zzz');

                // Log and handle exceptions
                \Log::error("Error fetching data for cadastral number $number: " . $e->getMessage());
                $results[] = [
                    'cad_number' => $number,
                    'error' => "An error occurred: " . $e->getMessage(),
                ];
            }
        }

        return view('pages.aktiv.kadastr_results', ['results' => $results]);
    }
}
