<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Donation
 *
 * Represents a financial contribution linked to a contact via Donor_ID.
 */
class Donation extends Model
{
    use HasFactory;

    // @ai-start: configure legacy table name and primary key for donations
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Donation_ID';

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
        'Donor_ID',
        'Donation_Amount',
        'Donation_Date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'Donation_Date' => 'datetime',
            'Donation_Amount' => 'float',
        ];
    }
    // @ai-end

    // @ai-start: relationship linking donation to donor contact
    /**
     * Get the contact who made this donation based on Donor_ID.
     *
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'Donor_ID', 'Donor_ID');
    }
    // @ai-end
}
