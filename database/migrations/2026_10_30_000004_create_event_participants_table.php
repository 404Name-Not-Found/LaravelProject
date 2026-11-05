<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// @ai-start: migration for event_participants pivot table to support testing and local schema reproducibility
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the event_participants table if it does not already exist.
     */
    public function up(): void
    {
        if (! Schema::hasTable('event_participants')) {
            Schema::create('event_participants', function (Blueprint $table) {
                $table->increments('Event_Participant_ID');
                $table->unsignedInteger('Event_ID')->nullable();
                $table->unsignedInteger('Contact_ID')->nullable();

                $table->foreign('Event_ID')
                    ->references('Event_ID')
                    ->on('events')
                    ->cascadeOnDelete();

                $table->foreign('Contact_ID')
                    ->references('Contact_ID')
                    ->on('contacts')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
// @ai-end
