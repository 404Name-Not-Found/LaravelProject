<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Contact
 *
 * Represents an individual person/member in the database.
 */
class Contact extends Model
{
    use HasFactory;

    // @ai-start: configure legacy table name and primary key for contacts
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Contact_ID';

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
        'Last_Name',
        'First_Name',
        'Date_of_Birth',
        'Gender',
        'Marital_Status',
        'Contact_Status',
        'Household_ID',
        'Household_Position',
        'Donor_ID',
        'Email_address',
        'Mobile_Phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'Date_of_Birth' => 'datetime',
        ];
    }
    // @ai-end

    // @ai-start: relationships for contacts (household, donations, events)
    /**
     * Get the household that this contact belongs to.
     *
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class, 'Household_ID', 'Household_ID');
    }

    /**
     * Get all donations made by this contact linked via Donor_ID.
     *
     * @return HasMany<Donation, $this>
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'Donor_ID', 'Donor_ID');
    }

    /**
     * Get all events this contact is registered for.
     *
     * @return BelongsToMany<Event, $this>
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'event_participants',
            'Contact_ID',
            'Event_ID',
            'Contact_ID',
            'Event_ID'
        );
    }

    /**
     * Get the participant registration records for this contact.
     *
     * @return HasMany<EventParticipant, $this>
     */
    public function eventParticipants(): HasMany
    {
        return $this->hasMany(EventParticipant::class, 'Contact_ID', 'Contact_ID');
    }
    // @ai-end
}
