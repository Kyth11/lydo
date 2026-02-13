<?php

namespace App\Http\Controllers;

use App\Models\Youth;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // SK users only see their barangay data
        if ($user && $user->isSK()) {

            $barangay = $user->barangay;

            $total = Youth::where('barangay', $barangay)->count();
            $male = Youth::where('barangay', $barangay)->where('sex', 'Male')->count();
            $female = Youth::where('barangay', $barangay)->where('sex', 'Female')->count();

            $barangayData = Youth::where('barangay', $barangay)
                ->selectRaw('barangay, COUNT(*) as total')
                ->groupBy('barangay')
                ->get();

            $barangayGenderData = Youth::where('barangay', $barangay)
                ->selectRaw("
                    barangay,
                    SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) as male,
                    SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) as female
                ")
                ->groupBy('barangay')
                ->get()
                ->map(function ($row) {
                    return [
                        'barangay' => $row->barangay,
                        'male' => (int) $row->male,
                        'female' => (int) $row->female,
                    ];
                });

            return view('dashboard.sk', compact(
                'barangay',
                'total',
                'male',
                'female',
                'barangayData',
                'barangayGenderData'
            ));
        }

        // ADMIN VIEW

        $total = Youth::count();
        $male = Youth::where('sex', 'Male')->count();
        $female = Youth::where('sex', 'Female')->count();

        $barangayData = Youth::selectRaw('barangay, COUNT(*) as total')
            ->groupBy('barangay')
            ->get();

        $barangayGenderData = Youth::selectRaw("
                barangay,
                SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) as female
            ")
            ->groupBy('barangay')
            ->get()
            ->map(function ($row) {
                return [
                    'barangay' => $row->barangay,
                    'male' => (int) $row->male,
                    'female' => (int) $row->female,
                ];
            });

        return view('dashboard.index', compact(
            'total',
            'male',
            'female',
            'barangayData',
            'barangayGenderData'
        ));
    }
}
