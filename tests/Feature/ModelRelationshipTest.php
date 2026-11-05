<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// @ai-start: test suite verifying Eloquent models and relationships in testing environment
class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test household can associate contacts.
     */
    public function test_household_has_many_contacts(): void
    {
        $household = Household::factory()->create();
        $contact = Contact::factory()->create([
            'Household_ID' => $household->Household_ID,
        ]);

        $this->assertCount(1, $household->contacts);
        $this->assertTrue($contact->household->is($household));
    }

    /**
     * Test contact has donations via Donor_ID.
     */
    public function test_contact_has_many_donations_via_donor_id(): void
    {
        $contact = Contact::factory()->create(['Donor_ID' => 5001]);
        $donation = Donation::factory()->create(['Donor_ID' => 5001]);

        $this->assertCount(1, $contact->donations);
        $this->assertTrue($donation->contact->is($contact));
    }

    /**
     * Test contact belongs to many events through event_participants.
     */
    public function test_contact_and_event_many_to_many_relationship(): void
    {
        $contact = Contact::factory()->create();
        $event = Event::factory()->create();

        $contact->events()->attach($event->Event_ID);

        $this->assertCount(1, $contact->events);
        $this->assertCount(1, $event->contacts);
    }
}
// @ai-end
