<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @ai-start: migration for households table to support testing and local schema reproducibility
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the households table if it does not already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('households')) {
            Schema::create('households', function (Blueprint $table) {
                $table->increments('Household_ID');
                $table->string('Household_Name', 255)->nullable();
                $table->string('Address_Line_1', 255)->nullable();
                $table->string('City', 255)->nullable();
                $table->string('State', 8)->nullable();
                $table->string('Zip_Code', 16)->nullable();
                $table->string('Home_Phone', 16)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
// @ai-end
