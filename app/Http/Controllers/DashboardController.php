<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSK = $user && $user->isSK();
        $barangay = $isSK ? $user->barangay : null;

        /*
        |--------------------------------------------------------------------------
        | BASIC COUNTS
        |--------------------------------------------------------------------------
        */

        $query = Youth::query();

        if ($isSK) {
            $query->where('barangay', $barangay);
        }

        $total = (clone $query)->count();
        $male = (clone $query)->where('sex', 'Male')->count();
        $female = (clone $query)->where('sex', 'Female')->count();


        /*
        |--------------------------------------------------------------------------
        | BARANGAY GENDER DATA
        |--------------------------------------------------------------------------
        */

        $barangayGenderQuery = Youth::selectRaw("
                barangay,
                SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) as female
            ");

        if ($isSK) {
            $barangayGenderQuery->where('barangay', $barangay);
        }

        $barangayGenderData = $barangayGenderQuery
            ->groupBy('barangay')
            ->get()
            ->map(function ($row) {
                return [
                    'barangay' => $row->barangay,
                    'male' => (int) $row->male,
                    'female' => (int) $row->female,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | MUNICIPAL COVERAGE
        |--------------------------------------------------------------------------
        */

        $totalPopulation = DB::table('barangay_populations')->sum('population');

        $totalProfiles = Youth::where('is_archived', 0)->count();

        $coveragePercent = $totalPopulation > 0
            ? round(($totalProfiles / $totalPopulation) * 100, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | BARANGAY COVERAGE
        |--------------------------------------------------------------------------
        */

        $barangayCoverageQuery = DB::table('barangay_populations');

        if ($isSK) {
            $barangayCoverageQuery->where('barangay', $barangay);
        }

        $barangayCoverage = $barangayCoverageQuery
            ->get()
            ->map(function ($b) {

                $profiles = Youth::where('barangay', $b->barangay)
                    ->where('is_archived', 0)
                    ->count();

                $percent = $b->population > 0
                    ? round(($profiles / $b->population) * 100, 1)
                    : 0;

                return [
                    'barangay' => $b->barangay,
                    'population' => $b->population,
                    'profiles' => $profiles,
                    'percent' => $percent
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | AGE GROUPS (ADMIN ONLY)
        |--------------------------------------------------------------------------
        */

        $ageGroups = [];

        $query = Youth::where('is_archived', 0);

        if ($isSK) {
            $query->where('barangay', $barangay);
        }

        $ageGroups = $query
            ->get()
            ->groupBy(function ($youth) {

                $age = Carbon::parse($youth->birthday)->age;

                if ($age >= 15 && $age <= 17) {
                    return '15-17';
                } elseif ($age >= 18 && $age <= 21) {
                    return '18-21';
                } elseif ($age >= 22 && $age <= 25) {
                    return '22-25';
                } elseif ($age >= 26 && $age <= 30) {
                    return '26-30';
                }
            })
            ->map(fn($group) => $group->count());


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', [
            'isSK' => $isSK,
            'barangay' => $barangay,
            'total' => $total,
            'male' => $male,
            'female' => $female,
            'barangayGenderData' => $barangayGenderData,
            'totalPopulation' => $totalPopulation,
            'totalProfiles' => $totalProfiles,
            'coveragePercent' => $coveragePercent,
            'barangayCoverage' => $barangayCoverage,
            'ageGroups' => $ageGroups
        ]);
    }
}
