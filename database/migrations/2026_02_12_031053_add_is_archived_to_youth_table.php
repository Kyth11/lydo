<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('youths', function ($table) {
            $table->boolean('is_archived')->default(0);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('youths', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
