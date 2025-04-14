<?php

namespace App\Http\Controllers;

use App\Models\Aktiv;
use App\Models\District;
use App\Models\Regions;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        // Get current date and user info for dashboard
        $currentDate = now()->format('Y-m-d H:i:s');
        $currentUser = auth()->user()->name ?? 'InvestUz';

        // Get all regions and districts for filtering
        $regions = Regions::all();
        $districts = District::all();

        // Total counts
        $totalAktivs = Aktiv::where('is_status_yer_tola', '!=', 1)->count();
        $totalYertolas = Aktiv::where('is_status_yer_tola', 1)->count();

        // Distribution by region
        $aktivsByRegion = DB::table('aktivs')
            ->join('sub_streets', 'aktivs.sub_street_id', '=', 'sub_streets.id')
            ->join('districts', 'sub_streets.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->where('aktivs.is_status_yer_tola', '!=', 1)
            ->select('regions.name_uz as region_name', DB::raw('count(*) as total'))
            ->groupBy('regions.name_uz')
            ->get();

        $yertolasByRegion = DB::table('aktivs')
            ->join('sub_streets', 'aktivs.sub_street_id', '=', 'sub_streets.id')
            ->join('districts', 'sub_streets.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->where('aktivs.is_status_yer_tola', 1)
            ->select('regions.name_uz as region_name', DB::raw('count(*) as total'))
            ->groupBy('regions.name_uz')
            ->get();

        // Distribution by district
        $aktivsByDistrict = DB::table('aktivs')
            ->join('sub_streets', 'aktivs.sub_street_id', '=', 'sub_streets.id')
            ->join('districts', 'sub_streets.district_id', '=', 'districts.id')
            ->where('aktivs.is_status_yer_tola', '!=', 1)
            ->select('districts.name_uz as district_name', DB::raw('count(*) as total'))
            ->groupBy('districts.name_uz')
            ->get();

        $yertolasByDistrict = DB::table('aktivs')
            ->join('sub_streets', 'aktivs.sub_street_id', '=', 'sub_streets.id')
            ->join('districts', 'sub_streets.district_id', '=', 'districts.id')
            ->where('aktivs.is_status_yer_tola', 1)
            ->select('districts.name_uz as district_name', DB::raw('count(*) as total'))
            ->groupBy('districts.name_uz')
            ->get();

        // Distribution by building type for aktivs
        $aktivsByBuildingType = DB::table('aktivs')
            ->where('is_status_yer_tola', '!=', 1)
            ->select('building_type', DB::raw('count(*) as total'))
            ->groupBy('building_type')
            ->get();

        // Document availability for aktivs - FIXED: Properly joining files table
        $aktivsWithDocuments = DB::table('aktivs')
            ->leftJoin('files', function ($join) {
                $join->on('aktivs.id', '=', 'files.aktiv_id')
                    ->where(function ($query) {
                        $query->where('files.path', 'like', '%.pdf')
                            ->orWhere('files.path', 'like', '%.doc%');
                    });
            })
            ->where('aktivs.is_status_yer_tola', '!=', 1)
            ->select(
                DB::raw('COUNT(DISTINCT aktivs.id) as total_aktivs'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NOT NULL THEN aktivs.id END) as with_documents'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NULL THEN aktivs.id END) as without_documents')
            )
            ->first();

        // Yertola usage statistics
        $yertolaUsageStats = DB::table('aktivs')
            ->where('is_status_yer_tola', 1)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN does_exists_yer_tola = 1 THEN 1 ELSE 0 END) as exists_count'),
                DB::raw('SUM(CASE WHEN does_can_we_use_yer_tola = 1 THEN 1 ELSE 0 END) as can_use_count'),
                DB::raw('SUM(CASE WHEN does_ijaraga_berilgan_yer_tola = 1 THEN 1 ELSE 0 END) as rented_count')
            )
            ->first();

        // Average metrics for yertolas
        $yertolaAverages = DB::table('aktivs')
            ->where('is_status_yer_tola', 1)
            ->select(
                DB::raw('AVG(ijaraga_berilgan_qismi_yer_tola) as avg_rented_area'),
                DB::raw('AVG(ijaraga_berilmagan_qismi_yer_tola) as avg_not_rented_area'),
                DB::raw('AVG(texnik_qismi_yer_tola) as avg_technical_area'),
                DB::raw('AVG(oylik_ijara_narxi_yer_tola) as avg_monthly_rent')
            )
            ->first();

        // Activity status for aktivs
        $aktivsStatusDistribution = DB::table('aktivs')
            ->where('is_status_yer_tola', '!=', 1)
            ->select('faoliyat_xolati', DB::raw('count(*) as total'))
            ->groupBy('faoliyat_xolati')
            ->get();

        // 24/7 operation status
        $aktivs24_7Distribution = DB::table('aktivs')
            ->where('is_status_yer_tola', '!=', 1)
            ->select('working_24_7', DB::raw('count(*) as total'))
            ->groupBy('working_24_7')
            ->get();

        // Count aktivs with photos - FIXED: Properly joining files table
        $aktivsWithPhotos = DB::table('aktivs')
            ->leftJoin('files', function ($join) {
                $join->on('aktivs.id', '=', 'files.aktiv_id')
                    ->where(function ($query) {
                        $query->where('files.path', 'like', '%.jpg')
                            ->orWhere('files.path', 'like', '%.jpeg')
                            ->orWhere('files.path', 'like', '%.png')
                            ->orWhere('files.path', 'like', '%.heic');
                    });
            })
            ->where('aktivs.is_status_yer_tola', '!=', 1)
            ->select(
                DB::raw('COUNT(DISTINCT aktivs.id) as total_aktivs'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NOT NULL THEN aktivs.id END) as with_photos'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NULL THEN aktivs.id END) as without_photos')
            )
            ->first();

        // Count yertolas with photos - FIXED: Properly joining files table
        $yertolasWithPhotos = DB::table('aktivs')
            ->leftJoin('files', function ($join) {
                $join->on('aktivs.id', '=', 'files.aktiv_id')
                    ->where(function ($query) {
                        $query->where('files.path', 'like', '%.jpg')
                            ->orWhere('files.path', 'like', '%.jpeg')
                            ->orWhere('files.path', 'like', '%.png')
                            ->orWhere('files.path', 'like', '%.heic');
                    });
            })
            ->where('aktivs.is_status_yer_tola', 1)
            ->select(
                DB::raw('COUNT(DISTINCT aktivs.id) as total_yertolas'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NOT NULL THEN aktivs.id END) as with_photos'),
                DB::raw('COUNT(DISTINCT CASE WHEN files.id IS NULL THEN aktivs.id END) as without_photos')
            )
            ->first();

        return view('pages.statistics.index_second', compact(
            'currentDate',
            'currentUser',
            'regions',
            'districts',
            'totalAktivs',
            'totalYertolas',
            'aktivsByRegion',
            'yertolasByRegion',
            'aktivsByDistrict',
            'yertolasByDistrict',
            'aktivsByBuildingType',
            'aktivsWithDocuments',
            'yertolaUsageStats',
            'yertolaAverages',
            'aktivsStatusDistribution',
            'aktivs24_7Distribution',
            'aktivsWithPhotos',
            'yertolasWithPhotos'
        ));
    }

    public function getFilteredData(Request $request)
    {
        $regionId = $request->input('region_id');
        $districtId = $request->input('district_id');

        $query = Aktiv::query();

        if ($regionId) {
            $query->whereHas('subStreet.district', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        if ($districtId) {
            $query->whereHas('subStreet', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        // Aktivs count (non-yertola)
        $totalAktivs = (clone $query)->where('is_status_yer_tola', '!=', 1)->count();

        // Yertolas count
        $totalYertolas = (clone $query)->where('is_status_yer_tola', 1)->count();

        // Aktiv status distribution
        $aktivsStatusDistribution = (clone $query)
            ->where('is_status_yer_tola', '!=', 1)
            ->select('faoliyat_xolati', DB::raw('count(*) as total'))
            ->groupBy('faoliyat_xolati')
            ->get();

        // Yertola status distribution
        $yertolaUsageStats = (clone $query)
            ->where('is_status_yer_tola', 1)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN does_exists_yer_tola = 1 THEN 1 ELSE 0 END) as exists_count'),
                DB::raw('SUM(CASE WHEN does_can_we_use_yer_tola = 1 THEN 1 ELSE 0 END) as can_use_count'),
                DB::raw('SUM(CASE WHEN does_ijaraga_berilgan_yer_tola = 1 THEN 1 ELSE 0 END) as rented_count')
            )
            ->first();

        return response()->json([
            'totalAktivs' => $totalAktivs,
            'totalYertolas' => $totalYertolas,
            'aktivsStatusDistribution' => $aktivsStatusDistribution,
            'yertolaUsageStats' => $yertolaUsageStats
        ]);
    }
}
