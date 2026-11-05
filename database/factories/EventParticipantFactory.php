<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventParticipant>
 */
class EventParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<EventParticipant>
     */
    protected $model = EventParticipant::class;

    // @ai-start: factory definition for event participants
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Event_ID' => Event::factory(),
            'Contact_ID' => Contact::factory(),
        ];
    }
    // @ai-end
}
