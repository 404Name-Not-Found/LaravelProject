{{-- @ai-start: reusable pop-up modal component for recording a donation for a contact --}}
<div id="donationModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="donationModalTitle" role="dialog" aria-modal="true">
    {{-- Dimmed backdrop overlay --}}
    <div id="donationModalBackdropOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>

    {{-- Modal dialog container --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
            {{-- Modal Header --}}
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="donationModalTitle" class="text-base font-bold text-slate-900">Make a Donation</h3>
                    </div>
                </div>
                <button type="button" id="closeDonationModalButton" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 hover:bg-slate-100 transition-colors cursor-pointer" title="Close modal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form method="POST" id="donationModalForm" action="">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    {{-- Target Member Display --}}
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Member:</span>
                        <div id="donationModalMemberNameDisplay" class="mt-1 text-sm font-semibold text-slate-800 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                            —
                        </div>
                    </div>

                    {{-- Donation Amount Input Field --}}
                    <div>
                        <label for="modalDonationAmountField" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Donation Amount ($) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-lg shadow-xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-slate-500 text-sm font-medium">$</span>
                            </div>
                            <input
                                type="number"
                                name="Donation_Amount"
                                id="modalDonationAmountField"
                                step="0.01"
                                min="0.01"
                                max="9999999.99"
                                placeholder="0.00"
                                required
                                class="block w-full rounded-lg border border-slate-300 pl-7 pr-3 py-2 text-sm text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                            >
                        </div>
                        @error('Donation_Amount')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Donation Date Input Field --}}
                    <div>
                        <label for="modalDonationDateField" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                            Donation Date <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="Donation_Date"
                            id="modalDonationDateField"
                            value="{{ date('Y-m-d') }}"
                            required
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer"
                        >
                        @error('Donation_Date')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Modal Action Buttons --}}
                <div class="bg-slate-50 px-6 py-3.5 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button type="button" id="cancelDonationModalButton" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        id="submitDonationModalButton"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer"
                    >
                        Save Donation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/* @ai-start: vanilla JavaScript handling donation modal opening, closing, and action assignment */
(function initializeDonationModalBehavior() {
    function setupDonationModalListeners() {
        const donationModalElement = document.getElementById('donationModal');
        const donationModalFormElement = document.getElementById('donationModalForm');
        const donationModalMemberNameDisplayElement = document.getElementById('donationModalMemberNameDisplay');
        const modalDonationAmountFieldElement = document.getElementById('modalDonationAmountField');
        const modalDonationDateFieldElement = document.getElementById('modalDonationDateField');
        const closeDonationModalButton = document.getElementById('closeDonationModalButton');
        const cancelDonationModalButton = document.getElementById('cancelDonationModalButton');
        const donationModalBackdropOverlayElement = document.getElementById('donationModalBackdropOverlay');

        if (!donationModalElement || !donationModalFormElement) {
            return;
        }

        // Open donation modal for a specific member contact
        function openDonationModal(contactId, contactFullName) {
            donationModalFormElement.action = '/people/' + encodeURIComponent(contactId) + '/donations';
            if (donationModalMemberNameDisplayElement) {
                donationModalMemberNameDisplayElement.textContent = contactFullName;
            }
            if (modalDonationAmountFieldElement) {
                modalDonationAmountFieldElement.value = '';
            }
            if (modalDonationDateFieldElement) {
                const todayIsoDateString = new Date().toISOString().split('T')[0];
                modalDonationDateFieldElement.value = todayIsoDateString;
            }
            donationModalElement.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if (modalDonationAmountFieldElement) {
                setTimeout(function () {
                    modalDonationAmountFieldElement.focus();
                }, 50);
            }
        }

        // Close donation modal
        function closeDonationModal() {
            donationModalElement.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Event delegation on document to handle clicks on any donate button (initial or dynamically reloaded)
        document.addEventListener('click', function (clickEvent) {
            const clickedDonateButton = clickEvent.target.closest('.open-donation-modal-button');
            if (clickedDonateButton) {
                clickEvent.preventDefault();
                const targetContactId = clickedDonateButton.getAttribute('data-contact-id');
                const targetContactName = clickedDonateButton.getAttribute('data-contact-name');
                openDonationModal(targetContactId, targetContactName);
            }
        });

        // Close button click listener
        if (closeDonationModalButton) {
            closeDonationModalButton.addEventListener('click', closeDonationModal);
        }

        // Cancel button click listener
        if (cancelDonationModalButton) {
            cancelDonationModalButton.addEventListener('click', closeDonationModal);
        }

        // Click outside modal (backdrop overlay) listener
        if (donationModalBackdropOverlayElement) {
            donationModalBackdropOverlayElement.addEventListener('click', closeDonationModal);
        }

        // Escape key listener
        document.addEventListener('keydown', function (keyboardEvent) {
            if (keyboardEvent.key === 'Escape' && !donationModalElement.classList.contains('hidden')) {
                closeDonationModal();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupDonationModalListeners);
    } else {
        setupDonationModalListeners();
    }
})();
/* @ai-end */
</script>
{{-- @ai-end --}}
