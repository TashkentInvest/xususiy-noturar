<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\District;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;


class HomeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $hasRoles = DB::table('model_has_roles')
            ->where('model_id', $userId)
            ->exists();

        if ($hasRoles) {
            // dd('dqw');
            // return view('pages.dashboard');
            return redirect()->route('aktivs.index');
        } else {
            // dd($hasRoles);
            return view('welcome');
        }
    }

    public function statistics()
    {
        $districts = District::withCount('aktives')->get(['name_uz', 'aktives_count']);
        return view('pages.statistics.index', compact('districts'));
    }


    public function optimize()
    {
        Artisan::call('cache:clear-optimize');
        return redirect()->back()->with('success', 'Optimized cache cleared successfully');
    }
}
