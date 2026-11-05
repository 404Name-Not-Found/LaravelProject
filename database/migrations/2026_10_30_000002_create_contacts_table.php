<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @ai-start: migration for contacts table to support testing and local schema reproducibility
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the contacts table if it does not already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->increments('Contact_ID');
                $table->string('Last_Name', 128);
                $table->string('First_Name', 128);
                $table->dateTime('Date_of_Birth')->nullable();
                $table->string('Gender', 16)->nullable();
                $table->string('Marital_Status', 16)->nullable();
                $table->string('Contact_Status', 16)->nullable();
                $table->unsignedInteger('Household_ID')->nullable();
                $table->string('Household_Position', 48)->nullable();
                $table->unsignedInteger('Donor_ID')->nullable()->unique();
                $table->string('Email_address', 255)->nullable();
                $table->string('Mobile_Phone', 16)->nullable();

                $table->foreign('Household_ID')
                    ->references('Household_ID')
                    ->on('households')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
// @ai-end
