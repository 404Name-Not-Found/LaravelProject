<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Class EventController
 *
 * Handles HTTP requests for listing events with dynamic search and pagination,
 * as well as displaying detailed event information and participant rosters.
 */
class EventController extends Controller
{
    // @ai-start: controller action to display a paginated and searchable list of events
    /**
     * Display a listing of events filtered by search term and paginated.
     *
     * @param  Request  $incomingHttpRequest  The incoming HTTP request containing search and pagination parameters.
     * @return View The rendered events index view.
     */
    public function index(Request $incomingHttpRequest): View
    {
        $requestedSearchTerm = trim((string) $incomingHttpRequest->query('search', ''));
        $requestedSearchTerm = mb_substr($requestedSearchTerm, 0, 100);

        $allowedRecordsPerPageOptions = [10, 25, 50];
        $recordsPerPageSelection = (int) $incomingHttpRequest->query('per_page', 10);

        if (! in_array($recordsPerPageSelection, $allowedRecordsPerPageOptions, true)) {
            $recordsPerPageSelection = 10;
        }

        $matchingEventsQuery = Event::query()->withCount('contacts');

        if ($requestedSearchTerm !== '') {
            $wildcardSearchPattern = '%'.$requestedSearchTerm.'%';
            $searchWordsList = preg_split('/\s+/', $requestedSearchTerm, -1, PREG_SPLIT_NO_EMPTY);

            $matchingEventsQuery->where(function ($subQuery) use ($wildcardSearchPattern, $searchWordsList) {
                // Event attributes: ID, title, description, start and end dates
                $subQuery->where('Event_ID', 'like', $wildcardSearchPattern)
                    ->orWhere('Event_Title', 'like', $wildcardSearchPattern)
                    ->orWhere('Event_Description', 'like', $wildcardSearchPattern)
                    ->orWhere('Event_Start_Date', 'like', $wildcardSearchPattern)
                    ->orWhere('Event_End_Date', 'like', $wildcardSearchPattern);

                // Registered attendee contacts category: search events by any attendee's name, email, phone, or donor ID
                $subQuery->orWhereHas('contacts', function ($attendeeQuery) use ($wildcardSearchPattern, $searchWordsList) {
                    $attendeeQuery->where('First_Name', 'like', $wildcardSearchPattern)
                        ->orWhere('Last_Name', 'like', $wildcardSearchPattern)
                        ->orWhere('Email_address', 'like', $wildcardSearchPattern)
                        ->orWhere('Mobile_Phone', 'like', $wildcardSearchPattern)
                        ->orWhere('Donor_ID', 'like', $wildcardSearchPattern);

                    // Multi-word attendee name search
                    if (count($searchWordsList) >= 2) {
                        $firstWordWildcard = '%'.$searchWordsList[0].'%';
                        $secondWordWildcard = '%'.$searchWordsList[1].'%';
                        $attendeeQuery->orWhere(function ($nameSubQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $nameSubQuery->where('First_Name', 'like', $firstWordWildcard)
                                ->where('Last_Name', 'like', $secondWordWildcard);
                        })->orWhere(function ($nameSubQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $nameSubQuery->where('First_Name', 'like', $secondWordWildcard)
                                ->where('Last_Name', 'like', $firstWordWildcard);
                        });
                    }
                });
            });
        }

        $matchingEventsQuery->orderBy('Event_Start_Date', 'desc');

        $paginatedEventsList = $matchingEventsQuery->paginate($recordsPerPageSelection)->withQueryString();

        return view('events.index', [
            'paginatedEventsList' => $paginatedEventsList,
            'currentSearchTerm' => $requestedSearchTerm,
            'selectedRecordsPerPage' => $recordsPerPageSelection,
        ]);
    }
    // @ai-end

    // @ai-start: controller action to display detailed event information and registered participants roster
    /**
     * Display the specified event with registered participant attendees.
     *
     * @param  Event  $event  The event model instance resolved via route model binding.
     * @return View The rendered event detail view.
     */
    public function show(Event $event): View
    {
        $event->load([
            'contacts' => function ($contactsQuery) {
                $contactsQuery->with('household')
                    ->orderBy('Last_Name', 'asc')
                    ->orderBy('First_Name', 'asc');
            },
        ]);

        return view('events.show', [
            'targetEventRecord' => $event,
        ]);
    }
    // @ai-end
}
