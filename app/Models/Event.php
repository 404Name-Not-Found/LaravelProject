<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Event
 *
 * Represents an event organized by the church.
 */
class Event extends Model
{
    use HasFactory;

    // @ai-start: configure legacy table name and primary key for events
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Event_ID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'Event_Title',
        'Event_Description',
        'Event_Start_Date',
        'Event_End_Date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'Event_Start_Date' => 'datetime',
            'Event_End_Date' => 'datetime',
        ];
    }
    // @ai-end

    // @ai-start: relationships for events (contacts and event participants)
    /**
     * Get all contacts registered for this event.
     *
     * @return BelongsToMany<Contact, $this>
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'event_participants',
            'Event_ID',
            'Contact_ID',
            'Event_ID',
            'Contact_ID'
        );
    }

    /**
     * Get all participant registration records for this event.
     *
     * @return HasMany<EventParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class, 'Event_ID', 'Event_ID');
    }
    // @ai-end
}
