<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Donation;
use App\Models\Household;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// @ai-start: feature tests verifying donations listing, dynamic multi-field search filtering, and pagination
class DonationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the donations index page loads successfully and displays records with formatted dates and amounts.
     */
    public function test_donations_index_page_loads_successfully_and_displays_donations(): void
    {
        $donorContact = Contact::factory()->create([
            'First_Name' => 'Eleanor',
            'Last_Name' => 'Roosevelt',
            'Donor_ID' => 31050,
        ]);

        $firstTestDonation = Donation::factory()->create([
            'Donor_ID' => $donorContact->Donor_ID,
            'Donation_Amount' => 250.00,
            'Donation_Date' => Carbon::create(2026, 9, 28, 14, 0, 0),
        ]);

        $secondTestDonation = Donation::factory()->create([
            'Donor_ID' => $donorContact->Donor_ID,
            'Donation_Amount' => 1250.50,
            'Donation_Date' => Carbon::create(2026, 10, 15, 10, 30, 0),
        ]);

        $httpResponse = $this->get(route('donations.index'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Donations Directory');
        $httpResponse->assertSee('Roosevelt, Eleanor');
        $httpResponse->assertSee('31050');
        $httpResponse->assertSee('Sep 28, 2026');
        $httpResponse->assertSee('$250.00');
        $httpResponse->assertSee('Oct 15, 2026');
        $httpResponse->assertSee('$1,250.50');
        $httpResponse->assertSee('#'.$firstTestDonation->Donation_ID);
        $httpResponse->assertSee('#'.$secondTestDonation->Donation_ID);
    }

    /**
     * Test that searching donations by donor name filters the donations list properly.
     */
    public function test_searching_donations_filters_results_by_donor_name(): void
    {
        $firstDonorContact = Contact::factory()->create([
            'First_Name' => 'Benjamin',
            'Last_Name' => 'Franklin',
            'Donor_ID' => 41001,
        ]);

        $secondDonorContact = Contact::factory()->create([
            'First_Name' => 'Alexander',
            'Last_Name' => 'Hamilton',
            'Donor_ID' => 41002,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $firstDonorContact->Donor_ID,
            'Donation_Amount' => 300.00,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $secondDonorContact->Donor_ID,
            'Donation_Amount' => 600.00,
        ]);

        $searchByNameResponse = $this->get(route('donations.index', ['search' => 'Franklin']));

        $searchByNameResponse->assertOk();
        $searchByNameResponse->assertSee('Franklin, Benjamin');
        $searchByNameResponse->assertSee('$300.00');
        $searchByNameResponse->assertDontSee('Hamilton, Alexander');
        $searchByNameResponse->assertDontSee('$600.00');
    }

    /**
     * Test that searching donations by donor ID and donation ID filters the records properly.
     */
    public function test_searching_donations_filters_results_by_donor_id_and_donation_id(): void
    {
        $targetDonorContact = Contact::factory()->create([
            'First_Name' => 'George',
            'Last_Name' => 'Washington',
            'Donor_ID' => 88776,
        ]);

        $otherDonorContact = Contact::factory()->create([
            'First_Name' => 'John',
            'Last_Name' => 'Adams',
            'Donor_ID' => 44332,
        ]);

        $matchingDonationRecord = Donation::factory()->create([
            'Donation_ID' => 99991,
            'Donor_ID' => $targetDonorContact->Donor_ID,
            'Donation_Amount' => 500.00,
        ]);

        $nonMatchingDonationRecord = Donation::factory()->create([
            'Donation_ID' => 88882,
            'Donor_ID' => $otherDonorContact->Donor_ID,
            'Donation_Amount' => 75.00,
        ]);

        $searchByDonorIdResponse = $this->get(route('donations.index', ['search' => '88776']));

        $searchByDonorIdResponse->assertOk();
        $searchByDonorIdResponse->assertSee('Washington, George');
        $searchByDonorIdResponse->assertSee('88776');
        $searchByDonorIdResponse->assertDontSee('Adams, John');

        $searchByDonationIdResponse = $this->get(route('donations.index', ['search' => '99991']));

        $searchByDonationIdResponse->assertOk();
        $searchByDonationIdResponse->assertSee('Washington, George');
        $searchByDonationIdResponse->assertDontSee('Adams, John');
    }

    /**
     * Test that searching donations by amount filters results properly.
     */
    public function test_searching_donations_filters_results_by_donation_amount(): void
    {
        $testDonorContact = Contact::factory()->create([
            'Donor_ID' => 55443,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $testDonorContact->Donor_ID,
            'Donation_Amount' => 987.50,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $testDonorContact->Donor_ID,
            'Donation_Amount' => 123.40,
        ]);

        $searchByAmountResponse = $this->get(route('donations.index', ['search' => '987.5']));

        $searchByAmountResponse->assertOk();
        $searchByAmountResponse->assertSee('$987.50');
        $searchByAmountResponse->assertDontSee('$123.40');
    }

    /**
     * Test that searching donations works across currency format with dollar sign, full name, and household categories.
     */
    public function test_searching_donations_across_multiple_categories(): void
    {
        $household = Household::factory()->create([
            'Household_Name' => 'Madison Residence',
            'City' => 'Montpelier',
        ]);

        $donor = Contact::factory()->create([
            'First_Name' => 'James',
            'Last_Name' => 'Madison',
            'Donor_ID' => 77112,
            'Household_ID' => $household->Household_ID,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $donor->Donor_ID,
            'Donation_Amount' => 450.00,
        ]);

        $otherDonor = Contact::factory()->create([
            'First_Name' => 'James',
            'Last_Name' => 'Monroe',
            'Donor_ID' => 77113,
        ]);

        Donation::factory()->create([
            'Donor_ID' => $otherDonor->Donor_ID,
            'Donation_Amount' => 150.00,
        ]);

        // Search with dollar sign currency symbol
        $searchByCurrencySymbolResponse = $this->get(route('donations.index', ['search' => '$450']));
        $searchByCurrencySymbolResponse->assertOk();
        $searchByCurrencySymbolResponse->assertSee('Madison, James');
        $searchByCurrencySymbolResponse->assertDontSee('Monroe, James');

        // Search by donor full name
        $searchByFullNameResponse = $this->get(route('donations.index', ['search' => 'James Madison']));
        $searchByFullNameResponse->assertOk();
        $searchByFullNameResponse->assertSee('Madison, James');
        $searchByFullNameResponse->assertDontSee('Monroe, James');

        // Search by donor household city
        $searchByCityResponse = $this->get(route('donations.index', ['search' => 'Montpelier']));
        $searchByCityResponse->assertOk();
        $searchByCityResponse->assertSee('Madison, James');
        $searchByCityResponse->assertDontSee('Monroe, James');
    }

    /**
     * Test that the per_page query parameter limits the number of donations returned per page.
     */
    public function test_donations_pagination_per_page_limits_results(): void
    {
        $sharedDonorContact = Contact::factory()->create([
            'Donor_ID' => 66778,
        ]);

        Donation::factory()->count(15)->create([
            'Donor_ID' => $sharedDonorContact->Donor_ID,
        ]);

        $tenPerPageResponse = $this->get(route('donations.index', ['per_page' => 10]));
        $tenPerPageResponse->assertOk();
        $this->assertCount(10, $tenPerPageResponse->viewData('paginatedDonationsList'));

        $twentyFivePerPageResponse = $this->get(route('donations.index', ['per_page' => 25]));
        $twentyFivePerPageResponse->assertOk();
        $this->assertCount(15, $twentyFivePerPageResponse->viewData('paginatedDonationsList'));
    }

    /**
     * Test that an appropriate empty state is displayed when no donations match the search term.
     */
    public function test_empty_state_is_displayed_when_no_donations_match(): void
    {
        $emptySearchResponse = $this->get(route('donations.index', ['search' => 'NonExistentDonorOrAmountXYZ']));

        $emptySearchResponse->assertOk();
        $emptySearchResponse->assertSee('No donations found');
        $emptySearchResponse->assertSee('Clear Search Filter');
    }
}
// @ai-end
