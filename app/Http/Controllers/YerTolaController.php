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
        $yertolas = Aktiv::all();
        return view('pages.yertola.index', compact('yertolas'));
    }

    public function create()
    {
        $regions = Regions::get();  // Assuming this needs no filtering
        $isSuperAdmin = auth()->id() === 1;  // Check if the user is the Super Admin
        $userDistrictId = auth()->user()->district_id;  // Get the district ID of the authenticated user

        $subStreets = SubStreet::all();
        $streets = Street::all();
        return view('pages.yertola.create', compact('subStreets', 'streets','regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_street_id' => 'required|exists:sub_streets,id',
            'street_id' => 'required|exists:streets,id',
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

        $validated['is_status_yer_tola'] = true;
        Aktiv::create($validated);

        return redirect()->route('pages.yertola.index')->with('success', 'YerTola created successfully.');
    }

    public function edit(Aktiv $yertola)
    {
        $subStreets = SubStreet::all();
        $streets = Street::all();
        return view('pages.yertola.edit', compact('yertola', 'subStreets', 'streets'));
    }

    public function update(Request $request, Aktiv $yertola)
    {
        $validated = $request->validate([
            'sub_street_id' => 'required|exists:sub_streets,id',
            'street_id' => 'required|exists:streets,id',
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
        return redirect()->route('pages.yertola.index')->with('success', 'YerTola updated successfully.');
    }

    public function destroy(Aktiv $yertola)
    {
        $yertola->delete();
        return redirect()->route('pages.yertola.index')->with('success', 'YerTola deleted successfully.');
    }
}
