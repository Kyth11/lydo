<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangayPopulationController extends Controller
{
    public function update(Request $request)
    {
        $barangay = $request->barangay;
        $population = $request->population;

        DB::table('barangay_populations')->updateOrInsert(
            ['barangay' => $barangay],
            ['population' => $population]
        );

        return response()->json([
            'success' => true
        ]);
    }
    public function updateAll(Request $request)
{
    foreach ($request->updates as $update) {

        DB::table('barangay_populations')
            ->where('barangay', $update['barangay'])
            ->update([
                'population' => $update['population']
            ]);

    }

    return response()->json([
        'success' => true
    ]);
}
}
