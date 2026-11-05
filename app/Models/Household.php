<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Household
 *
 * Represents a household grouping for one or more contacts in the database.
 */
class Household extends Model
{
    use HasFactory;

    // @ai-start: configure legacy table name and primary key for households
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'households';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'Household_ID';

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
        'Household_Name',
        'Address_Line_1',
        'City',
        'State',
        'Zip_Code',
        'Home_Phone',
    ];
    // @ai-end

    // @ai-start: relationship between household and member contacts
    /**
     * Get all contacts that belong to this household.
     *
     * @return HasMany<Contact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'Household_ID', 'Household_ID');
    }
    // @ai-end
}
