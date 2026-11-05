<?php

namespace Database\Factories;

use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Household>
 */
class HouseholdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Household>
     */
    protected $model = Household::class;

    // @ai-start: factory definition for households
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Household_Name' => fake()->lastName(),
            'Address_Line_1' => fake()->streetAddress(),
            'City' => fake()->city(),
            'State' => fake()->stateAbbr(),
            'Zip_Code' => fake()->postcode(),
            'Home_Phone' => fake()->phoneNumber(),
        ];
    }
    // @ai-end
}
