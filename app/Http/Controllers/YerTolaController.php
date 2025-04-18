<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktiv;
use App\Models\Regions;
use App\Models\SubStreet;
use App\Models\Street;

class YerTolaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->roles[0]->name ?? '';

        $query = Aktiv::query()
            ->with('files')
            ->orderByDesc('id')
            ->where('is_status_yer_tola', 1);

        if ($userRole === 'Employee') {
            $query->whereHas('street', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($userRole === 'Manager') {
            $query->whereHas('street.district', function ($q) use ($user) {
                $q->where('id', $user->district_id);
            });
        }

        $yertolas = $query->paginate(15);

        return view('pages.yertola.index', compact('yertolas'));
    }

    public function create()
    {
        $regions = Regions::get();  // Assuming this needs no filtering
        $isSuperAdmin = auth()->id() === 1;  // Check if the user is the Super Admin
        $userDistrictId = auth()->user()->district_id;  // Get the district ID of the authenticated user

        $subStreets = SubStreet::all();
        $streets = Street::all();
        return view('pages.yertola.create', compact('subStreets', 'streets', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'geolokatsiya'     => 'nullable',
            'latitude'         => 'nullable',
            'longitude'        => 'nullable',
            'sub_street_id' => 'required',
            'company_management_id' => 'nullable',
            'street_id' => 'required',
            'does_exists_yer_tola' => 'nullable',
            'balance_keeper' => 'nullable|string',
            'stir' => 'nullable|string',
            'does_can_we_use_yer_tola' => 'nullable',
            'ijaraga_berilgan_qismi_yer_tola' => 'nullable|numeric',
            'ijaraga_berilmagan_qismi_yer_tola' => 'nullable|numeric',
            'texnik_qismi_yer_tola' => 'nullable|numeric',
            'oylik_ijara_narxi_yer_tola' => 'nullable|numeric',
            'faoliyat_turi' => 'nullable|array', // Ensure it's an array

            'does_yer_tola_ijaraga_berish_mumkin' => 'nullable',
            'umumiy_maydoni_yer_tola' => 'nullable',
            'files.*'          => 'required',

        ]);

        // Convert the array to JSON before storing
        if (isset($validated['faoliyat_turi'])) {
            $validated['faoliyat_turi'] = json_encode($validated['faoliyat_turi']);
            // Alternative: Store as a comma-separated string
            // $validated['faoliyat_turi'] = implode(',', $validated['faoliyat_turi']);
        }

        // Add the hidden field value
        $validated['is_status_yer_tola'] = true;

        // Save the data
        $yertola = Aktiv::create($validated);

        if ($request->hasFile('files')) {
            $filePaths = [];
            foreach ($request->file('files') as $file) {
                $filePaths[] = [
                    'path' => $file->store('assets', 'public'),
                    'aktiv_id' => $yertola->id,
                ];
            }
            // Use batch insert for file paths
            $yertola->files()->insert($filePaths);
        }

        return redirect()->route('yertola.index')->with('success', 'YerTola created successfully.');
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

            return view('pages.yertola.edit', compact('aktiv', 'regions', 'districts', 'streets', 'substreets'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading Aktiv data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading Aktiv data. Please try again.');
        }
    }


    public function update(Request $request, Aktiv $yertola)
    {
        $validated = $request->validate([
            'geolokatsiya'     => 'nullable',
            'latitude'         => 'nullable',
            'longitude'        => 'nullable',
            'sub_street_id' => 'required',
            'street_id' => 'required',
            'does_exists_yer_tola' => 'nullable',
            'balance_keeper' => 'nullable|string',
            'stir' => 'nullable|string',
            'does_can_we_use_yer_tola' => 'nullable',
            'ijaraga_berilgan_qismi_yer_tola' => 'nullable|numeric',
            'ijaraga_berilmagan_qismi_yer_tola' => 'nullable|numeric',
            'texnik_qismi_yer_tola' => 'nullable|numeric',
            'oylik_ijara_narxi_yer_tola' => 'nullable|numeric',
            'faoliyat_turi' => 'nullable|array',
            'umumiy_maydoni_yer_tola' => 'nullable',


            'does_yer_tola_ijaraga_berish_mumkin' => 'nullable',

        ]);

        $yertola->update($validated);
        return redirect()->route('yertola.index')->with('success', 'YerTola updated successfully.');
    }
    public function destroy(Aktiv $yertola)
    {
        // Ensure the model exists
        if (!$yertola) {
            return redirect()->route('yertola.index')->with('error', 'Yer Tola not found.');
        }

        // Delete associated files
        if ($yertola->files()->exists()) { // Use a proper relationship check
            foreach ($yertola->files as $file) {
                \Storage::disk('public')->delete($file->path);
                $file->delete();
            }
        }

        $yertola->delete(); // No need to call find() again

        return redirect()->route('yertola.index')->with('success', 'Yer Tola deleted successfully.');
    }

    public function exportCSV()
    {
        $user = auth()->user();
        $districtId = $user->district_id;

        $fileName = 'ер_тўлалар_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $yertolas = Aktiv::where('is_status_yer_tola', 1)
            ->whereHas('street', fn ($q) => $q->where('district_id', $districtId))
            ->with('street')
            ->get();

        $columns = [
            'Объект номи',
            'Манзил',
            'Умумий ер тўла майдони',
            'Ижарага берилган қисми',
            'Ижарага берилмаган қисми',
            'Ойлик ижара нархи',
            'Фаолият тури',
            'Ижарага бериш мумкинми',
        ];

        $callback = function () use ($yertolas, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($yertolas as $item) {
                fputcsv($handle, [
                    $item->object_name,
                    $item->location,
                    $item->umumiy_maydoni_yer_tola,
                    $item->ijaraga_berilgan_qismi_yer_tola,
                    $item->ijaraga_berilmagan_qismi_yer_tola,
                    $item->oylik_ijara_narxi_yer_tola,
                    implode(', ', (array)$item->faoliyat_turi),
                    $item->does_yer_tola_ijaraga_berish_mumkin ? 'Ҳа' : 'Йўқ',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }

}
