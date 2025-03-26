<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktiv;
use App\Models\Regions;
use App\Models\SubStreet;
use App\Models\Street;

class YerTolaController extends Controller
{
    public function index()
    {
        $yertolas = Aktiv::where('is_status_yer_tola', 1)->get();
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
            'geolokatsiya'     => 'required|string',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'sub_street_id' => 'required',
            'street_id' => 'required',
            'does_exists_yer_tola' => 'required',
            'balance_keeper' => 'nullable|string',
            'stir' => 'nullable|string',
            'does_can_we_use_yer_tola' => 'required',
            'ijaraga_berilgan_qismi_yer_tola' => 'nullable|numeric',
            'ijaraga_berilmagan_qismi_yer_tola' => 'nullable|numeric',
            'texnik_qismi_yer_tola' => 'nullable|numeric',
            'oylik_ijara_narxi_yer_tola' => 'nullable|numeric',
            'faoliyat_turi' => 'required|array', // Ensure it's an array
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
        Aktiv::create($validated);

        return redirect()->route('yertola.index')->with('success', 'YerTola created successfully.');
    }

    public function edit(Aktiv $yertola)
    {
        $regions = Regions::get();

        // dd('wqe ');
        try {

            $yertola->load('subStreet.district.region');


            $districts = optional($yertola->subStreet->district->region)->districts ?? collect();
            $streets = optional($yertola->subStreet->district)->streets ?? collect();
            $substreets = optional($yertola->subStreet->district)->subStreets ?? collect();

            return view('pages.yertola.edit', compact('yertola', 'subStreets', 'streets', 'regions', 'districts'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading Aktiv data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading Aktiv data. Please try again.');
        }
    }

    public function update(Request $request, Aktiv $yertola)
    {
        $validated = $request->validate([
            'geolokatsiya'     => 'required|string',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'sub_street_id' => 'required',
            'street_id' => 'required',
            'does_exists_yer_tola' => 'required',
            'balance_keeper' => 'nullable|string',
            'stir' => 'nullable|string',
            'does_can_we_use_yer_tola' => 'required',
            'ijaraga_berilgan_qismi_yer_tola' => 'nullable|numeric',
            'ijaraga_berilmagan_qismi_yer_tola' => 'nullable|numeric',
            'texnik_qismi_yer_tola' => 'nullable|numeric',
            'oylik_ijara_narxi_yer_tola' => 'nullable|numeric',
            'faoliyat_turi' => 'required|array',
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
}
