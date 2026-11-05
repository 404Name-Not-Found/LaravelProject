<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Event>
     */
    protected $model = Event::class;

    // @ai-start: factory definition for events
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+2 hours');

        return [
            'Event_Title' => fake()->sentence(3),
            'Event_Description' => fake()->paragraph(),
            'Event_Start_Date' => $startDate,
            'Event_End_Date' => $endDate,
        ];
    }
    // @ai-end
}
