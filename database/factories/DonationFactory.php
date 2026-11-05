<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Donation>
     */
    protected $model = Donation::class;

    // @ai-start: factory definition for donations
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Donor_ID' => function () {
                return Contact::factory()->create()->Donor_ID;
            },
            'Donation_Amount' => fake()->randomFloat(2, 10, 5000),
            'Donation_Date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
    // @ai-end
}
