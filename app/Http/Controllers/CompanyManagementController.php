<?php

namespace App\Http\Controllers;

use App\Models\CompanyManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyManagementController extends Controller
{
    /**
     * Search for company management by name or INN
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 3) {
            return response()->json([]);
        }

        $companies = CompanyManagement::where(function($q) use ($query) {
            // Search by organization name (case-insensitive)
            $q->where('organization', 'like', "%{$query}%")
              // Or search by INN
              ->orWhere('inn', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'organization', 'inn', 'representative', 'phone']);

        return response()->json($companies);
    }

    /**
     * Create a new company management via API
     */
    public function apiCreate(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'organization' => 'required|string|max:255',
            'inn' => 'required|string|max:9',
            'representative' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'service_phone' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Check if company with same INN already exists
        $existingCompany = CompanyManagement::where('inn', $request->inn)->first();
        if ($existingCompany) {
            return response()->json(['error' => 'Бу СТИР рақами билан компания аллақачон мавжуд'], 422);
        }

        // Create new company
        $company = new CompanyManagement();
        $company->organization = $request->organization;
        $company->inn = $request->inn;
        $company->representative = $request->representative;
        $company->phone = $request->phone;
        $company->service_phone = $request->service_phone;
        $company->district = $request->district;
        $company->address = $request->address;
        $company->user_id = Auth::id();
        $company->save();

        return response()->json($company);
    }
}
