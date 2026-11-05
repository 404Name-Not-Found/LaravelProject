<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Event;
use App\Models\Household;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// @ai-start: feature tests verifying events listing, dynamic search filtering, pagination, detail view, and registration workflow
class EventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the events index page loads successfully and displays event titles and formatted dates.
     */
    public function test_events_index_page_loads_successfully_and_displays_events(): void
    {
        $firstTestEvent = Event::factory()->create([
            'Event_Title' => 'Fall Harvest Festival',
            'Event_Start_Date' => Carbon::create(2026, 10, 15, 18, 30),
            'Event_End_Date' => Carbon::create(2026, 10, 15, 21, 0),
        ]);

        $secondTestEvent = Event::factory()->create([
            'Event_Title' => 'Community Bible Study',
            'Event_Start_Date' => Carbon::create(2026, 11, 2, 9, 0),
            'Event_End_Date' => Carbon::create(2026, 11, 2, 10, 30),
        ]);

        $httpResponse = $this->get(route('events.index'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Events Directory');
        $httpResponse->assertSee('Fall Harvest Festival');
        $httpResponse->assertSee('Oct 15, 2026 6:30 PM');
        $httpResponse->assertSee('Community Bible Study');
        $httpResponse->assertSee('Nov 2, 2026 9:00 AM');
    }

    /**
     * Test that searching events by title and description filters results properly.
     */
    public function test_searching_events_filters_results_by_title_and_description(): void
    {
        $campingRetreatEvent = Event::factory()->create([
            'Event_Title' => 'Summer Youth Camp',
            'Event_Description' => 'Annual mountain camping trip for students.',
        ]);

        $banquetDinnerEvent = Event::factory()->create([
            'Event_Title' => 'Annual Thanksgiving Banquet',
            'Event_Description' => 'Formal dinner celebration in the main hall.',
        ]);

        $searchByTitleResponse = $this->get(route('events.index', ['search' => 'Youth Camp']));
        $searchByTitleResponse->assertOk();
        $searchByTitleResponse->assertSee('Summer Youth Camp');
        $searchByTitleResponse->assertDontSee('Annual Thanksgiving Banquet');

        $searchByDescriptionResponse = $this->get(route('events.index', ['search' => 'Formal dinner']));
        $searchByDescriptionResponse->assertOk();
        $searchByDescriptionResponse->assertSee('Annual Thanksgiving Banquet');
        $searchByDescriptionResponse->assertDontSee('Summer Youth Camp');
    }

    /**
     * Test that events can be searched by registered attendee contact names.
     */
    public function test_searching_events_by_registered_attendee_category(): void
    {
        $retreatEvent = Event::factory()->create([
            'Event_Title' => 'Winter Leadership Retreat',
        ]);

        $attendeeContact = Contact::factory()->create([
            'First_Name' => 'Thomas',
            'Last_Name' => 'Jefferson',
        ]);
        $retreatEvent->contacts()->attach($attendeeContact->Contact_ID);

        $unrelatedEvent = Event::factory()->create([
            'Event_Title' => 'Community Yard Sale',
        ]);

        $searchByAttendeeResponse = $this->get(route('events.index', ['search' => 'Thomas Jefferson']));
        $searchByAttendeeResponse->assertOk();
        $searchByAttendeeResponse->assertSee('Winter Leadership Retreat');
        $searchByAttendeeResponse->assertDontSee('Community Yard Sale');
    }

    /**
     * Test that the per_page parameter limits the number of event records per page.
     */
    public function test_events_pagination_per_page_limits_results(): void
    {
        Event::factory()->count(15)->create();

        $tenPerPageResponse = $this->get(route('events.index', ['per_page' => 10]));
        $tenPerPageResponse->assertOk();
        $this->assertCount(10, $tenPerPageResponse->viewData('paginatedEventsList'));

        $twentyFivePerPageResponse = $this->get(route('events.index', ['per_page' => 25]));
        $twentyFivePerPageResponse->assertOk();
        $this->assertCount(15, $twentyFivePerPageResponse->viewData('paginatedEventsList'));
    }

    /**
     * Test that the event detail page displays event info and registered participant attendees.
     */
    public function test_event_detail_page_displays_event_details_and_registered_attendees(): void
    {
        $testHousehold = Household::factory()->create([
            'Household_Name' => 'Lincoln Household',
        ]);

        $registeredContact = Contact::factory()->create([
            'First_Name' => 'Abraham',
            'Last_Name' => 'Lincoln',
            'Email_address' => 'abraham.lincoln@example.com',
            'Household_ID' => $testHousehold->Household_ID,
        ]);

        $targetEvent = Event::factory()->create([
            'Event_Title' => 'Leadership Development Workshop',
            'Event_Description' => 'Intensive weekend workshop focusing on church leadership.',
        ]);

        $targetEvent->contacts()->attach($registeredContact->Contact_ID);

        $httpResponse = $this->get(route('events.show', $targetEvent));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Leadership Development Workshop');
        $httpResponse->assertSee('Intensive weekend workshop focusing on church leadership.');
        $httpResponse->assertSee('Lincoln, Abraham');
        $httpResponse->assertSee('abraham.lincoln@example.com');
        $httpResponse->assertSee('Lincoln Household');
        $httpResponse->assertSee('Total Registered:');
    }

    /**
     * Test that the event registration form renders with the contact details and available events.
     */
    public function test_event_registration_form_displays_for_contact(): void
    {
        $targetContact = Contact::factory()->create([
            'First_Name' => 'Benjamin',
            'Last_Name' => 'Franklin',
            'Email_address' => 'ben.franklin@example.com',
        ]);

        $availableEvent = Event::factory()->create([
            'Event_Title' => 'Inventors & Leaders Fellowship',
        ]);

        $httpResponse = $this->get(route('people.registrations.create', $targetContact));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Event Registration');
        $httpResponse->assertSee('Benjamin Franklin');
        $httpResponse->assertSee('ben.franklin@example.com');
        $httpResponse->assertSee('Inventors & Leaders Fellowship');
    }

    /**
     * Test that submitting a valid event registration saves to the database and redirects to the people list.
     */
    public function test_valid_event_registration_stores_and_redirects_to_people_index(): void
    {
        $targetContact = Contact::factory()->create([
            'First_Name' => 'Martha',
            'Last_Name' => 'Washington',
        ]);

        $targetEvent = Event::factory()->create([
            'Event_Title' => 'Spring Gardening Day',
        ]);

        $formSubmissionPayload = [
            'Event_ID' => $targetEvent->Event_ID,
        ];

        $submissionResponse = $this->post(
            route('people.registrations.store', $targetContact),
            $formSubmissionPayload
        );

        $submissionResponse->assertRedirect(route('people.index'));
        $submissionResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_participants', [
            'Event_ID' => $targetEvent->Event_ID,
            'Contact_ID' => $targetContact->Contact_ID,
        ]);

        $this->assertTrue($targetContact->events()->where('events.Event_ID', $targetEvent->Event_ID)->exists());
    }

    /**
     * Test that invalid or missing Event_ID in registration submission fails validation.
     */
    public function test_registration_validation_rejects_missing_and_nonexistent_event(): void
    {
        $targetContact = Contact::factory()->create();

        $emptySubmissionResponse = $this->post(
            route('people.registrations.store', $targetContact),
            []
        );
        $emptySubmissionResponse->assertSessionHasErrors(['Event_ID']);

        $nonexistentEventSubmissionResponse = $this->post(
            route('people.registrations.store', $targetContact),
            ['Event_ID' => 99999999]
        );
        $nonexistentEventSubmissionResponse->assertSessionHasErrors(['Event_ID']);
    }

    /**
     * Test that duplicate registration for the same contact and event is rejected by validation.
     */
    public function test_duplicate_event_registration_is_rejected_by_validation(): void
    {
        $targetContact = Contact::factory()->create();
        $targetEvent = Event::factory()->create();

        // First registration succeeds
        $targetContact->events()->attach($targetEvent->Event_ID);

        // Attempting to register again for the exact same event
        $duplicateSubmissionResponse = $this->post(
            route('people.registrations.store', $targetContact),
            ['Event_ID' => $targetEvent->Event_ID]
        );

        $duplicateSubmissionResponse->assertSessionHasErrors(['Event_ID']);
        $this->assertCount(1, $targetContact->events()->where('events.Event_ID', $targetEvent->Event_ID)->get());
    }
}
// @ai-end
