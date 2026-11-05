<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Class ContactController
 *
 * Handles HTTP requests for listing contacts with dynamic search and pagination,
 * as well as displaying detailed information and history for an individual contact.
 */
class ContactController extends Controller
{
    // @ai-start: controller action to display a paginated and searchable list of contacts
    /**
     * Display a listing of contacts filtered by search term and paginated.
     *
     * @param  Request  $incomingHttpRequest  The incoming HTTP request containing search and pagination parameters.
     * @return View The rendered people index view with matching contacts.
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

        $matchingContactsQuery = Contact::query()->with('household');

        if ($requestedSearchTerm !== '') {
            $wildcardSearchPattern = '%'.$requestedSearchTerm.'%';
            $searchWordsList = preg_split('/\s+/', $requestedSearchTerm, -1, PREG_SPLIT_NO_EMPTY);
            $cleanedNumericSearchTerm = str_replace(['$', ','], '', $requestedSearchTerm);

            $matchingContactsQuery->where(function ($subQuery) use ($wildcardSearchPattern, $searchWordsList, $cleanedNumericSearchTerm) {
                // Personal contact categories and identifiers
                $subQuery->where('Contact_ID', 'like', $wildcardSearchPattern)
                    ->orWhere('First_Name', 'like', $wildcardSearchPattern)
                    ->orWhere('Last_Name', 'like', $wildcardSearchPattern)
                    ->orWhere('Email_address', 'like', $wildcardSearchPattern)
                    ->orWhere('Mobile_Phone', 'like', $wildcardSearchPattern)
                    ->orWhere('Contact_Status', 'like', $wildcardSearchPattern)
                    ->orWhere('Household_Position', 'like', $wildcardSearchPattern)
                    ->orWhere('Gender', 'like', $wildcardSearchPattern)
                    ->orWhere('Marital_Status', 'like', $wildcardSearchPattern)
                    ->orWhere('Donor_ID', 'like', $wildcardSearchPattern)
                    ->orWhere('Date_of_Birth', 'like', $wildcardSearchPattern);

                // Multi-word full name search (e.g. "Alice Smith" or "Smith Alice")
                if (count($searchWordsList) >= 2) {
                    $firstWordWildcard = '%'.$searchWordsList[0].'%';
                    $secondWordWildcard = '%'.$searchWordsList[1].'%';
                    $subQuery->orWhere(function ($nameSubQuery) use ($firstWordWildcard, $secondWordWildcard) {
                        $nameSubQuery->where(function ($forwardNameQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $forwardNameQuery->where('First_Name', 'like', $firstWordWildcard)
                                ->where('Last_Name', 'like', $secondWordWildcard);
                        })->orWhere(function ($reversedNameQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $reversedNameQuery->where('First_Name', 'like', $secondWordWildcard)
                                ->where('Last_Name', 'like', $firstWordWildcard);
                        });
                    });
                }

                // Household category fields
                $subQuery->orWhereHas('household', function ($householdQuery) use ($wildcardSearchPattern) {
                    $householdQuery->where('Household_Name', 'like', $wildcardSearchPattern)
                        ->orWhere('Address_Line_1', 'like', $wildcardSearchPattern)
                        ->orWhere('City', 'like', $wildcardSearchPattern)
                        ->orWhere('State', 'like', $wildcardSearchPattern)
                        ->orWhere('Zip_Code', 'like', $wildcardSearchPattern)
                        ->orWhere('Home_Phone', 'like', $wildcardSearchPattern);
                });

                // Events category (events this contact participates in)
                $subQuery->orWhereHas('events', function ($eventsQuery) use ($wildcardSearchPattern) {
                    $eventsQuery->where('Event_Title', 'like', $wildcardSearchPattern)
                        ->orWhere('Event_Description', 'like', $wildcardSearchPattern);
                });

                // Donations category (donations made by this contact)
                if (is_numeric($cleanedNumericSearchTerm)) {
                    $subQuery->orWhereHas('donations', function ($donationsQuery) use ($cleanedNumericSearchTerm) {
                        $donationsQuery->where('Donation_Amount', 'like', '%'.$cleanedNumericSearchTerm.'%');
                    });
                }
            });
        }

        $matchingContactsQuery->orderBy('Last_Name', 'asc')
            ->orderBy('First_Name', 'asc');

        $paginatedContactsList = $matchingContactsQuery->paginate($recordsPerPageSelection)->withQueryString();

        $availableEventsList = Event::query()
            ->orderBy('Event_Start_Date', 'desc')
            ->get();

        return view('people.index', [
            'paginatedContactsList' => $paginatedContactsList,
            'currentSearchTerm' => $requestedSearchTerm,
            'selectedRecordsPerPage' => $recordsPerPageSelection,
            'availableEventsList' => $availableEventsList,
        ]);
    }
    // @ai-end

    // @ai-start: controller action to display detailed profile, event history, and donation history for a contact
    /**
     * Display the specified contact along with their household, event participation, and donation history.
     *
     * @param  Contact  $contact  The contact model instance resolved via route model binding.
     * @return View The rendered contact detail view.
     */
    public function show(Contact $contact): View
    {
        $contact->load([
            'household',
            'events' => function ($eventsQuery) {
                $eventsQuery->orderBy('Event_Start_Date', 'desc');
            },
            'donations' => function ($donationsQuery) {
                $donationsQuery->orderBy('Donation_Date', 'desc');
            },
        ]);

        $availableEventsList = Event::query()
            ->orderBy('Event_Start_Date', 'desc')
            ->get();

        return view('people.show', [
            'targetContactRecord' => $contact,
            'availableEventsList' => $availableEventsList,
        ]);
    }
    // @ai-end
}
