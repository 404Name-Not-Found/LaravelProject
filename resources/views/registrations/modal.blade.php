{{-- @ai-start: reusable pop-up modal component for registering a member for an event --}}
<div id="eventRegistrationModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    {{-- Dimmed backdrop overlay --}}
    <div id="modalBackdropOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

    {{-- Modal dialog container --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
            {{-- Modal Header --}}
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-sky-100 text-sky-700 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-base font-bold text-slate-900">Register for an Event</h3>
                    </div>
                </div>
                <button type="button" id="closeRegistrationModalButton" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 hover:bg-slate-100 transition-colors cursor-pointer" title="Close modal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form method="POST" id="eventRegistrationModalForm" action="">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    {{-- Target Member Display --}}
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Member:</span>
                        <div id="modalMemberNameDisplay" class="mt-1 text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                            —
                        </div>
                    </div>

                    {{-- Event Selection Field --}}
                    <div>
                        <label for="modalEventSelectField" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Select Event <span class="text-rose-500">*</span>
                        </label>
                        @if (isset($availableEventsList) && $availableEventsList->count() > 0)
                            <select
                                id="modalEventSelectField"
                                name="Event_ID"
                                required
                                class="block w-full border border-slate-300 rounded-lg py-2 px-3 text-sm font-medium text-slate-700 bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors cursor-pointer"
                            >
                                <option value="" disabled selected>-- Choose an Event --</option>
                                @foreach ($availableEventsList as $eventOption)
                                    <option value="{{ $eventOption->Event_ID }}">
                                        {{ $eventOption->Event_Title }} &middot; {{ $eventOption->Event_Start_Date ? $eventOption->Event_Start_Date->format('M j, Y g:i A') : 'Date TBA' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('Event_ID')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                                There are currently no scheduled events available in the database.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Modal Action Buttons --}}
                <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button type="button" id="cancelRegistrationModalButton" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        id="submitRegistrationModalButton"
                        @disabled(!isset($availableEventsList) || $availableEventsList->isEmpty())
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs transition-colors cursor-pointer"
                    >
                        Complete Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/* @ai-start: vanilla JavaScript handling event registration modal opening, closing, and action assignment */
(function initializeRegistrationModalBehavior() {
    function setupModalListeners() {
        const registrationModalElement = document.getElementById('eventRegistrationModal');
        const registrationModalFormElement = document.getElementById('eventRegistrationModalForm');
        const modalMemberNameDisplayElement = document.getElementById('modalMemberNameDisplay');
        const modalEventSelectFieldElement = document.getElementById('modalEventSelectField');
        const closeRegistrationModalButton = document.getElementById('closeRegistrationModalButton');
        const cancelRegistrationModalButton = document.getElementById('cancelRegistrationModalButton');
        const modalBackdropOverlayElement = document.getElementById('modalBackdropOverlay');

        if (!registrationModalElement || !registrationModalFormElement) {
            return;
        }

        // Open registration modal for a specific member contact
        function openRegistrationModal(contactId, contactFullName) {
            registrationModalFormElement.action = '/people/' + encodeURIComponent(contactId) + '/registrations';
            if (modalMemberNameDisplayElement) {
                modalMemberNameDisplayElement.textContent = contactFullName;
            }
            if (modalEventSelectFieldElement) {
                modalEventSelectFieldElement.selectedIndex = 0;
            }
            registrationModalElement.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        // Close registration modal
        function closeRegistrationModal() {
            registrationModalElement.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Event delegation on document to handle clicks on any register button (initial or dynamically reloaded)
        document.addEventListener('click', function (clickEvent) {
            const clickedRegisterButton = clickEvent.target.closest('.open-registration-modal-button');
            if (clickedRegisterButton) {
                clickEvent.preventDefault();
                const targetContactId = clickedRegisterButton.getAttribute('data-contact-id');
                const targetContactName = clickedRegisterButton.getAttribute('data-contact-name');
                openRegistrationModal(targetContactId, targetContactName);
            }
        });

        // Close button click listener
        if (closeRegistrationModalButton) {
            closeRegistrationModalButton.addEventListener('click', closeRegistrationModal);
        }

        // Cancel button click listener
        if (cancelRegistrationModalButton) {
            cancelRegistrationModalButton.addEventListener('click', closeRegistrationModal);
        }

        // Click outside modal (backdrop overlay) listener
        if (modalBackdropOverlayElement) {
            modalBackdropOverlayElement.addEventListener('click', closeRegistrationModal);
        }

        // Escape key listener
        document.addEventListener('keydown', function (keyboardEvent) {
            if (keyboardEvent.key === 'Escape' && !registrationModalElement.classList.contains('hidden')) {
                closeRegistrationModal();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupModalListeners);
    } else {
        setupModalListeners();
    }
})();
/* @ai-end */
</script>
{{-- @ai-end --}}
