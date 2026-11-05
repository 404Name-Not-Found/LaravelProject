<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Donation;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// @ai-start: feature tests verifying application landing page, metrics overview, and quick navigation
class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the landing page renders successfully and displays the GenevaTrac welcome hero and directories.
     */
    public function test_landing_page_loads_successfully_and_displays_overview(): void
    {
        $testContact = Contact::factory()->create([
            'First_Name' => 'Lydia',
            'Last_Name' => 'Sellon',
        ]);

        $testEvent = Event::factory()->create([
            'Event_Title' => 'Annual Community Picnic',
        ]);

        $testDonation = Donation::factory()->create([
            'Donor_ID' => $testContact->Donor_ID,
            'Donation_Amount' => 350.00,
        ]);

        $httpResponse = $this->get(route('home'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('Welcome to GenevaTrac');
        $httpResponse->assertSee('People');
        $httpResponse->assertSee('Events');
        $httpResponse->assertSee('Donations');
        $httpResponse->assertSee('Annual Community Picnic');
        $httpResponse->assertSee('$350.00');
        $httpResponse->assertSee('oldmain-web.jpg');
    }

    /**
     * Test that the landing page displays accurate metric counts for church records.
     */
    public function test_landing_page_displays_accurate_metrics_counts(): void
    {
        Contact::factory()->count(3)->create();
        Event::factory()->count(2)->create();

        $httpResponse = $this->get(route('home'));

        $httpResponse->assertOk();
        $httpResponse->assertSee('3');
        $httpResponse->assertSee('Members');
        $httpResponse->assertSee('2');
        $httpResponse->assertSee('Events');
    }
}
// @ai-end
