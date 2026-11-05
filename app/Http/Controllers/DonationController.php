<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Contact;
use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class DonationController
 *
 * Handles HTTP requests for listing donations with dynamic multi-field search and pagination.
 */
class DonationController extends Controller
{
    // @ai-start: controller action to display a paginated and searchable list of donations
    /**
     * Display a listing of donations filtered by search term and paginated.
     *
     * @param  Request  $incomingHttpRequest  The incoming HTTP request containing search and pagination parameters.
     * @return View The rendered donations index view with paginated records.
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

        $matchingDonationsQuery = Donation::query()->with('contact.household');

        if ($requestedSearchTerm !== '') {
            $wildcardSearchPattern = '%'.$requestedSearchTerm.'%';
            $searchWordsList = preg_split('/\s+/', $requestedSearchTerm, -1, PREG_SPLIT_NO_EMPTY);
            $cleanedNumericSearchTerm = str_replace(['$', ','], '', $requestedSearchTerm);

            $matchingDonationsQuery->where(function ($subQuery) use ($wildcardSearchPattern, $searchWordsList, $cleanedNumericSearchTerm) {
                // Donation attributes
                $subQuery->where('Donation_ID', 'like', $wildcardSearchPattern)
                    ->orWhere('Donor_ID', 'like', $wildcardSearchPattern)
                    ->orWhere('Donation_Amount', 'like', $wildcardSearchPattern)
                    ->orWhere('Donation_Date', 'like', $wildcardSearchPattern);

                // Currency cleaned amount search (e.g. user types "$250.00" or "$250")
                if ($cleanedNumericSearchTerm !== '' && is_numeric($cleanedNumericSearchTerm)) {
                    $subQuery->orWhere('Donation_Amount', 'like', '%'.$cleanedNumericSearchTerm.'%');
                }

                // Donor contact category: First Name, Last Name, full name, email, phone
                $subQuery->orWhereHas('contact', function ($contactQuery) use ($wildcardSearchPattern, $searchWordsList) {
                    $contactQuery->where('First_Name', 'like', $wildcardSearchPattern)
                        ->orWhere('Last_Name', 'like', $wildcardSearchPattern)
                        ->orWhere('Email_address', 'like', $wildcardSearchPattern)
                        ->orWhere('Mobile_Phone', 'like', $wildcardSearchPattern);

                    // Multi-word donor name search
                    if (count($searchWordsList) >= 2) {
                        $firstWordWildcard = '%'.$searchWordsList[0].'%';
                        $secondWordWildcard = '%'.$searchWordsList[1].'%';
                        $contactQuery->orWhere(function ($nameSubQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $nameSubQuery->where('First_Name', 'like', $firstWordWildcard)
                                ->where('Last_Name', 'like', $secondWordWildcard);
                        })->orWhere(function ($nameSubQuery) use ($firstWordWildcard, $secondWordWildcard) {
                            $nameSubQuery->where('First_Name', 'like', $secondWordWildcard)
                                ->where('Last_Name', 'like', $firstWordWildcard);
                        });
                    }

                    // Donor household category
                    $contactQuery->orWhereHas('household', function ($householdQuery) use ($wildcardSearchPattern) {
                        $householdQuery->where('Household_Name', 'like', $wildcardSearchPattern)
                            ->orWhere('City', 'like', $wildcardSearchPattern);
                    });
                });
            });
        }

        $matchingDonationsQuery->orderBy('Donation_Date', 'desc')
            ->orderBy('Donation_ID', 'desc');

        $paginatedDonationsList = $matchingDonationsQuery->paginate($recordsPerPageSelection)->withQueryString();

        return view('donations.index', [
            'paginatedDonationsList' => $paginatedDonationsList,
            'currentSearchTerm' => $requestedSearchTerm,
            'selectedRecordsPerPage' => $recordsPerPageSelection,
        ]);
    }
    // @ai-end

    // @ai-start: controller action to render the standalone donation creation form for a contact
    /**
     * Show the form for making a donation for a contact.
     *
     * @param  Contact  $contact  The contact model instance resolved via route model binding.
     * @return View The rendered donation creation form view.
     */
    public function create(Contact $contact): View
    {
        $contact->load('household');

        return view('donations.create', [
            'targetContactRecord' => $contact,
        ]);
    }
    // @ai-end

    // @ai-start: controller action to store a valid donation and auto-assign Donor_ID if needed
    /**
     * Store a new donation record for the specified contact.
     *
     * Assigns the next sequential Donor_ID if the contact has not previously donated.
     *
     * @param  StoreDonationRequest  $incomingDonationRequest  The validated donation request payload.
     * @param  Contact  $contact  The donor contact model instance.
     * @return RedirectResponse Redirect to the people directory with a flash success message.
     */
    public function store(StoreDonationRequest $incomingDonationRequest, Contact $contact): RedirectResponse
    {
        $validatedDonationData = $incomingDonationRequest->validated();

        // Assign next available Donor_ID (highest existing Donor_ID + 1) if contact lacks one
        if (is_null($contact->Donor_ID)) {
            $highestExistingDonorId = (int) Contact::max('Donor_ID');
            $nextAvailableDonorId = $highestExistingDonorId > 0 ? $highestExistingDonorId + 1 : 10001;
            $contact->update(['Donor_ID' => $nextAvailableDonorId]);
            $contact->Donor_ID = $nextAvailableDonorId;
        }

        $recordedDonationRecord = Donation::create([
            'Donor_ID' => $contact->Donor_ID,
            'Donation_Amount' => $validatedDonationData['Donation_Amount'],
            'Donation_Date' => $validatedDonationData['Donation_Date'],
        ]);

        return redirect()->route('people.index')->with(
            'success',
            'Successfully recorded donation of $'.number_format((float) $recordedDonationRecord->Donation_Amount, 2)." for {$contact->First_Name} {$contact->Last_Name}."
        );
    }
    // @ai-end
}
