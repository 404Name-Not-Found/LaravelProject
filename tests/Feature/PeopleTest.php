<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// @ai-start: feature tests verifying people listing, dynamic search filtering, pagination, and detailed member profile view
class PeopleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the people index page renders successfully and displays members.
     */
    public function test_people_index_page_loads_successfully_and_displays_contacts(): void
    {
        $testHousehold = Household::factory()->create([
            'Household_Name' => 'Smith Household',
        ]);

        $firstTestContact = Contact::factory()->create([
            'First_Name' => 'Alice',
            'Last_Name' => 'Smith',
            'Household_ID' => $testHousehold->Household_ID,
        ]);

        $secondTestContact = Contact::factory()->create([
            'First_Name' => 'Bob',
            'Last_Name' => 'Jones',
        ]);

        $httpResponse = $this->get(route('people.index'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('People Directory');
        $httpResponse->assertSee('Smith, Alice');
        $httpResponse->assertSee('Jones, Bob');
        $httpResponse->assertSee('Smith Household');
    }

    /**
     * Test that searching by contact name or email filters the people table correctly.
     */
    public function test_searching_contacts_filters_results_by_name_and_email(): void
    {
        $matchingTargetContact = Contact::factory()->create([
            'First_Name' => 'Grace',
            'Last_Name' => 'Hopper',
            'Email_address' => 'grace.hopper@example.com',
        ]);

        $nonMatchingContact = Contact::factory()->create([
            'First_Name' => 'Charles',
            'Last_Name' => 'Babbage',
            'Email_address' => 'charles.babbage@example.com',
        ]);

        $searchByNameResponse = $this->get(route('people.index', ['search' => 'Hopper']));
        $searchByNameResponse->assertOk();
        $searchByNameResponse->assertSee('Hopper, Grace');
        $searchByNameResponse->assertDontSee('Babbage, Charles');

        $searchByEmailResponse = $this->get(route('people.index', ['search' => 'grace.hopper@example.com']));
        $searchByEmailResponse->assertOk();
        $searchByEmailResponse->assertSee('Hopper, Grace');
        $searchByEmailResponse->assertDontSee('Babbage, Charles');
    }

    /**
     * Test that contacts can be searched across any category including full name, household city, and participated events.
     */
    public function test_searching_contacts_across_multiple_categories(): void
    {
        $household = Household::factory()->create([
            'Household_Name' => 'Curie Estate',
            'City' => 'Geneva',
        ]);

        $contact = Contact::factory()->create([
            'First_Name' => 'Marie',
            'Last_Name' => 'Curie',
            'Household_ID' => $household->Household_ID,
        ]);

        $event = Event::factory()->create([
            'Event_Title' => 'Radioactivity Symposium',
        ]);
        $contact->events()->attach($event->Event_ID);

        $otherContact = Contact::factory()->create([
            'First_Name' => 'Isaac',
            'Last_Name' => 'Newton',
        ]);

        // Search by full name
        $fullNameResponse = $this->get(route('people.index', ['search' => 'Marie Curie']));
        $fullNameResponse->assertOk();
        $fullNameResponse->assertSee('Curie, Marie');
        $fullNameResponse->assertDontSee('Newton, Isaac');

        // Search by household city category
        $cityResponse = $this->get(route('people.index', ['search' => 'Geneva']));
        $cityResponse->assertOk();
        $cityResponse->assertSee('Curie, Marie');
        $cityResponse->assertDontSee('Newton, Isaac');

        // Search by participated event category
        $eventResponse = $this->get(route('people.index', ['search' => 'Radioactivity']));
        $eventResponse->assertOk();
        $eventResponse->assertSee('Curie, Marie');
        $eventResponse->assertDontSee('Newton, Isaac');
    }

    /**
     * Test that changing the per-page parameter adjusts pagination limits.
     */
    public function test_per_page_parameter_adjusts_records_count_per_page(): void
    {
        Contact::factory()->count(15)->create();

        $tenPerPageResponse = $this->get(route('people.index', ['per_page' => 10]));
        $tenPerPageResponse->assertOk();
        $this->assertCount(10, $tenPerPageResponse->viewData('paginatedContactsList'));

        $twentyFivePerPageResponse = $this->get(route('people.index', ['per_page' => 25]));
        $twentyFivePerPageResponse->assertOk();
        $this->assertCount(15, $twentyFivePerPageResponse->viewData('paginatedContactsList'));
    }

    /**
     * Test that a contact profile detail view displays personal info, event history, and donation history.
     */
    public function test_person_detail_page_displays_contact_info_events_and_donations(): void
    {
        $testHousehold = Household::factory()->create([
            'Household_Name' => 'Hamilton Household',
            'Address_Line_1' => '123 Freedom Way',
            'City' => 'Yorktown',
            'State' => 'VA',
        ]);

        $designatedDonorId = 77889;

        $targetContact = Contact::factory()->create([
            'First_Name' => 'Alexander',
            'Last_Name' => 'Hamilton',
            'Email_address' => 'alexander@treasury.gov',
            'Mobile_Phone' => '555-123-4567',
            'Household_ID' => $testHousehold->Household_ID,
            'Donor_ID' => $designatedDonorId,
        ]);

        $participatingEvent = Event::factory()->create([
            'Event_Title' => 'Community Leadership Summit',
        ]);
        $targetContact->events()->attach($participatingEvent->Event_ID);

        $recordedDonation = Donation::factory()->create([
            'Donor_ID' => $designatedDonorId,
            'Donation_Amount' => 1500.00,
        ]);

        $httpResponse = $this->get(route('people.show', $targetContact));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Alexander Hamilton');
        $httpResponse->assertSee('alexander@treasury.gov');
        $httpResponse->assertSee('555-123-4567');
        $httpResponse->assertSee('Hamilton Household');
        $httpResponse->assertSee('123 Freedom Way');
        $httpResponse->assertSee('Community Leadership Summit');
        $httpResponse->assertSee('$1,500.00');
        $httpResponse->assertSee('donationModal', false);
        $httpResponse->assertSee('eventRegistrationModal', false);
    }

    /**
     * Test that the standalone donation creation page renders successfully for a contact.
     */
    public function test_donation_create_form_renders_successfully(): void
    {
        $targetContact = Contact::factory()->create([
            'First_Name' => 'Abigail',
            'Last_Name' => 'Smith',
        ]);

        $donationResponse = $this->get(route('people.donations.create', $targetContact));

        $donationResponse->assertOk();
        $donationResponse->assertSee('Make a Donation');
        $donationResponse->assertSee('Abigail Smith');
    }

    /**
     * Test that saving a donation stores the record and redirects to people index with a success flash message.
     */
    public function test_saving_donation_creates_record_and_redirects_with_success_message(): void
    {
        $donorContact = Contact::factory()->create([
            'First_Name' => 'Thomas',
            'Last_Name' => 'Paine',
            'Donor_ID' => 54321,
        ]);

        $storeResponse = $this->post(route('people.donations.store', $donorContact), [
            'Donation_Amount' => 750.50,
            'Donation_Date' => '2026-11-04',
        ]);

        $storeResponse->assertRedirect(route('people.index'));
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('donations', [
            'Donor_ID' => 54321,
            'Donation_Amount' => 750.50,
        ]);
    }

    /**
     * Test that saving a donation for a contact without a Donor_ID auto-assigns the highest Donor_ID + 1.
     */
    public function test_saving_donation_auto_assigns_highest_donor_id_plus_one(): void
    {
        Contact::factory()->create([
            'Donor_ID' => 30050,
        ]);

        $newDonorContact = Contact::factory()->create([
            'First_Name' => 'Samuel',
            'Last_Name' => 'Adams',
            'Donor_ID' => null,
        ]);

        $storeResponse = $this->post(route('people.donations.store', $newDonorContact), [
            'Donation_Amount' => 125.00,
            'Donation_Date' => '2026-11-04',
        ]);

        $storeResponse->assertRedirect(route('people.index'));
        $storeResponse->assertSessionHas('success');

        $newDonorContact->refresh();
        $this->assertEquals(30051, $newDonorContact->Donor_ID);

        $this->assertDatabaseHas('donations', [
            'Donor_ID' => 30051,
            'Donation_Amount' => 125.00,
        ]);
    }

    /**
     * Test that saving a donation validates required fields and positive amounts.
     */
    public function test_saving_donation_validation_rejects_invalid_inputs(): void
    {
        $targetContact = Contact::factory()->create();

        $invalidResponse = $this->post(route('people.donations.store', $targetContact), [
            'Donation_Amount' => -50.00,
            'Donation_Date' => 'not-a-date',
        ]);

        $invalidResponse->assertSessionHasErrors(['Donation_Amount', 'Donation_Date']);
    }

    /**
     * Test that the event registration and donation modals are embedded in the people index view.
     */
    public function test_people_index_embeds_event_registration_modal(): void
    {
        $testContact = Contact::factory()->create([
            'First_Name' => 'Hannah',
            'Last_Name' => 'Adams',
        ]);

        $testEvent = Event::factory()->create([
            'Event_Title' => 'Midweek Prayer Meeting',
        ]);

        $httpResponse = $this->get(route('people.index'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('eventRegistrationModal', false);
        $httpResponse->assertSee('donationModal', false);
        $httpResponse->assertSee('Midweek Prayer Meeting');
        $httpResponse->assertSee('open-registration-modal-button');
        $httpResponse->assertSee('open-donation-modal-button');
    }
}
// @ai-end
