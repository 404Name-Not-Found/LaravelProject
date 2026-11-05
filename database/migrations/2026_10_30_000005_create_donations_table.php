<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @ai-start: migration for donations table to support testing and local schema reproducibility
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the donations table if it does not already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('donations')) {
            Schema::create('donations', function (Blueprint $table) {
                $table->increments('Donation_ID');
                $table->unsignedInteger('Donor_ID')->nullable();
                $table->float('Donation_Amount')->nullable();
                $table->dateTime('Donation_Date')->nullable();

                $table->foreign('Donor_ID')
                    ->references('Donor_ID')
                    ->on('contacts')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
// @ai-end
