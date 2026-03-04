<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | BASIC AUTH FIELDS
            |--------------------------------------------------------------------------
            */

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            /*
            |--------------------------------------------------------------------------
            | ROLE & ACCESS CONTROL
            |--------------------------------------------------------------------------
            */

            $table->enum('role', ['admin', 'sk'])->default('sk');
            $table->string('position')->nullable();     // e.g. Chairperson
            $table->string('barangay')->nullable();     // assigned barangay

            /*
            |--------------------------------------------------------------------------
            | SECURITY & FEATURE CONTROLS
            |--------------------------------------------------------------------------
            */

            // Protect destructive actions (archive/delete verification toggle)
            $table->boolean('action_protection')->default(true);

            // Controls public KK registration availability
            $table->boolean('kk_register_enabled')->default(false);

            // Hard-disable user login
            $table->boolean('is_disabled')->default(false);

            /*
            |--------------------------------------------------------------------------
            | LARAVEL SYSTEM FIELDS
            |--------------------------------------------------------------------------
            */

            $table->rememberToken();
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | PASSWORD RESET TOKENS
        |--------------------------------------------------------------------------
        */

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        /*
        |--------------------------------------------------------------------------
        | SESSIONS TABLE (Database Driver)
        |--------------------------------------------------------------------------
        */

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
