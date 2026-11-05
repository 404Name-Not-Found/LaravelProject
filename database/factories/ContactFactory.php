<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Contact>
     */
    protected $model = Contact::class;

    // @ai-start: factory definition for contacts
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Last_Name' => fake()->lastName(),
            'First_Name' => fake()->firstName(),
            'Date_of_Birth' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'Gender' => fake()->randomElement(['Male', 'Female']),
            'Marital_Status' => fake()->randomElement(['Married', 'Single', 'Divorced', 'Widowed']),
            'Contact_Status' => fake()->randomElement(['Active', 'Inactive']),
            'Household_ID' => Household::factory(),
            'Household_Position' => fake()->randomElement(['Head of Household', 'Spouse', 'Child', 'Other Adult']),
            'Donor_ID' => fake()->unique()->numberBetween(20000, 99999),
            'Email_address' => fake()->safeEmail(),
            'Mobile_Phone' => fake()->numerify('###-###-####'),
        ];
    }
    // @ai-end
}
