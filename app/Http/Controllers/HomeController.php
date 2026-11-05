<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Donation;
use App\Models\Event;
use Illuminate\Contracts\View\View;

/**
 * Class HomeController
 *
 * Handles HTTP requests for the application's landing page, providing high-level
 * church management summary statistics and quick navigation links.
 */
class HomeController extends Controller
{
    // @ai-start: controller action to render the GenevaTrac application landing page
    /**
     * Display the main landing page with church metrics overview and quick navigation.
     *
     * @return View The rendered home landing page view.
     */
    public function index(): View
    {
        $totalRegisteredContactsCount = Contact::count();
        $totalScheduledEventsCount = Event::count();
        $totalDonationRecordsCount = Donation::count();
        $totalContributedAmountSum = (float) Donation::sum('Donation_Amount');

        $upcomingEventsPreviewList = Event::query()
            ->orderBy('Event_Start_Date', 'desc')
            ->limit(3)
            ->get();

        return view('home', [
            'totalRegisteredContactsCount' => $totalRegisteredContactsCount,
            'totalScheduledEventsCount' => $totalScheduledEventsCount,
            'totalDonationRecordsCount' => $totalDonationRecordsCount,
            'totalContributedAmountSum' => $totalContributedAmountSum,
            'upcomingEventsPreviewList' => $upcomingEventsPreviewList,
        ]);
    }
    // @ai-end
}
