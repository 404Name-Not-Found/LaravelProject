<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRegistrationRequest;
use App\Models\Contact;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class EventRegistrationController
 *
 * Handles HTTP requests for displaying the event registration form for a contact
 * and saving the registration record while preventing duplicate registrations.
 */
class EventRegistrationController extends Controller
{
    // @ai-start: controller action to render the event registration form for a contact
    /**
     * Show the form for registering a contact for an event.
     *
     * @param  Contact  $contact  The contact model instance resolved via route model binding.
     * @return View The rendered registration form view.
     */
    public function create(Contact $contact): View
    {
        $contact->load('household');

        $availableEventsList = Event::query()
            ->orderBy('Event_Start_Date', 'desc')
            ->get();

        $alreadyRegisteredEventIdsList = $contact->events()
            ->pluck('events.Event_ID')
            ->toArray();

        return view('registrations.create', [
            'targetContactRecord' => $contact,
            'availableEventsList' => $availableEventsList,
            'alreadyRegisteredEventIdsList' => $alreadyRegisteredEventIdsList,
        ]);
    }
    // @ai-end

    // @ai-start: controller action to store a valid event registration and redirect to people list
    /**
     * Store a new event registration for the given contact.
     *
     * @param  StoreEventRegistrationRequest  $incomingRegistrationRequest  The validated registration request.
     * @param  Contact  $contact  The contact model instance to register.
     * @return RedirectResponse A redirect back to the people directory with a flash success message.
     */
    public function store(StoreEventRegistrationRequest $incomingRegistrationRequest, Contact $contact): RedirectResponse
    {
        $validatedData = $incomingRegistrationRequest->validated();
        $selectedEventId = (int) $validatedData['Event_ID'];

        $selectedEventRecord = Event::findOrFail($selectedEventId);

        $contact->events()->attach($selectedEventRecord->Event_ID);

        return redirect()->route('people.index')->with(
            'success',
            "Successfully registered {$contact->First_Name} {$contact->Last_Name} for {$selectedEventRecord->Event_Title}."
        );
    }
    // @ai-end
}
