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
            ['barangay' => 'Awang', 'population' => 0],
            ['barangay' => 'Bagocboc', 'population' => 0],
            ['barangay' => 'Barra', 'population' => 0],
            ['barangay' => 'Bonbon', 'population' => 0],
            ['barangay' => 'Cauyunan', 'population' => 0],
            ['barangay' => 'Igpit', 'population' => 0],
            ['barangay' => 'Limunda', 'population' => 0],
            ['barangay' => 'Luyong Bonbon', 'population' => 0],
            ['barangay' => 'Malanang', 'population' => 0],
            ['barangay' => 'Nangcaon', 'population' => 0],
            ['barangay' => 'Patag', 'population' => 0],
            ['barangay' => 'Poblacion', 'population' => 0],
            ['barangay' => 'Taboc', 'population' => 0],
            ['barangay' => 'Tingalan', 'population' => 0],
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
