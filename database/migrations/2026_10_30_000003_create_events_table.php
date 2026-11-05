<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @ai-start: migration for events table to support testing and local schema reproducibility
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the events table if it does not already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->increments('Event_ID');
                $table->string('Event_Title', 256)->nullable();
                $table->text('Event_Description')->nullable();
                $table->dateTime('Event_Start_Date')->nullable();
                $table->dateTime('Event_End_Date')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
// @ai-end
