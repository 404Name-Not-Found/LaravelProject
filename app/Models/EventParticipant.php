<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EventParticipant
 *
 * Represents the registration pivot record connecting a contact to an event.
 */
class EventParticipant extends Model
{
    use HasFactory;

    // @ai-start: configure legacy table name and primary key for event participants
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_participants';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Event_Participant_ID';

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
        'Event_ID',
        'Contact_ID',
    ];
    // @ai-end

    // @ai-start: relationships for event participant record
    /**
     * Get the event associated with this registration.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'Event_ID', 'Event_ID');
    }

    /**
     * Get the contact associated with this registration.
     *
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'Contact_ID', 'Contact_ID');
    }
    // @ai-end
}
