<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('youths', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFYING INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('profile_photo')->nullable();
            $table->enum('sex', ['Male', 'Female']);
            $table->enum('gender', ['LGBTQAI+', 'Prefer not to say']);
            $table->integer('age');
            $table->string('civil_status');
            $table->date('birthday');

            $table->text('home_address');
            $table->string('religion')->nullable();
            $table->string('education');

            $table->enum('is_sk_voter', ['Yes', 'No']);
            $table->boolean('is_osy')->default(false);
            $table->boolean('is_isy')->default(false);
            $table->boolean('is_unemployed')->default(false);
            $table->boolean('is_employed')->default(false);
            $table->boolean('is_self_employed')->default(false);
            $table->boolean('is_4ps')->default(false);
            $table->boolean('is_ip')->default(false);
            $table->boolean('is_pwd')->default(false);


            $table->text('skills');
            $table->text('preferred_skills');
            $table->string('source_of_income')->nullable();
            $table->string('contact_number');

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            $table->string('region');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('purok_zone')->nullable();

            /*
            |--------------------------------------------------------------------------
            | FAMILY COMPOSITION
            |--------------------------------------------------------------------------
            */

            $table->json('family_members')->nullable();

            /*
            |--------------------------------------------------------------------------
            | SYSTEM CONTROL
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_archived')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youths');
    }
};
