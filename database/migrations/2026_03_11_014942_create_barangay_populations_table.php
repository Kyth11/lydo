<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangay_populations', function (Blueprint $table) {
            $table->id();
            $table->string('barangay')->unique();
            $table->integer('population')->default(0);
            $table->timestamps();
        });

        DB::table('barangay_populations')->insert([
            ['barangay' => 'AWANG', 'population' => 0],
            ['barangay' => 'BAGOCBOC', 'population' => 0],
            ['barangay' => 'BARRA', 'population' => 0],
            ['barangay' => 'BONBON', 'population' => 0],
            ['barangay' => 'CAUYUNAN', 'population' => 0],
            ['barangay' => 'IGPIT', 'population' => 0],
            ['barangay' => 'LIMUNDA', 'population' => 0],
            ['barangay' => 'LUYONG BONBON', 'population' => 0],
            ['barangay' => 'MALANANG', 'population' => 0],
            ['barangay' => 'NANGCAON', 'population' => 0],
            ['barangay' => 'PATAG', 'population' => 0],
            ['barangay' => 'POBLACION', 'population' => 0],
            ['barangay' => 'TABOC', 'population' => 0],
            ['barangay' => 'TINGALAN', 'population' => 0],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_populations');
    }
};
