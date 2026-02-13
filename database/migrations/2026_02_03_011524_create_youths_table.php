<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('youths', function (Blueprint $table) {
            $table->id();

            // IDENTIFYING INFORMATION
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('sex', ['Male', 'Female']);
            $table->integer('age');
            $table->date('birthday');

            $table->text('home_address');
            $table->string('religion')->nullable();
            $table->string('education');
            $table->boolean('is_osy')->default(false);
            $table->boolean('is_isy')->default(false);
            $table->boolean('is_working_youth')->default(false);

            $table->text('skills')->nullable();
            $table->string('source_of_income')->nullable();
            $table->string('contact_number')->nullable();

            // LOCATION
            $table->string('region');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('purok_zone')->nullable();

            // FAMILY COMPOSITION (JSON storage)
            $table->json('family_members')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('youths');
    }
};
