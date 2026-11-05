{{-- @ai-start: view template for the donations directory listing with dynamic live search, date formatting, and pagination --}}
@extends('layouts.app')

@section('title', 'Donations Directory')

@section('content')
<div class="space-y-6">

    {{-- Page Header Section --}}
    <div class="relative flex flex-col sm:flex-row items-center justify-between sm:justify-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 text-center">Donations Directory</h1>
        <div class="sm:absolute sm:right-0 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg px-4 py-2 shadow-xs self-center">
            Total Donations: <span id="totalDonationsCountDisplay" class="font-semibold text-slate-900">{{ number_format($paginatedDonationsList->total()) }}</span>
        </div>
    </div>

    {{-- Controls Bar: Live Search Box and Records Per Page Selector --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('donations.index') }}" id="donationsFilterForm" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            {{-- Live Search Input --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    id="liveSearchInput"
                    name="search"
                    value="{{ $currentSearchTerm }}"
                    placeholder="Search donations..."
                    class="block w-full pl-10 pr-10 py-2 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                    autocomplete="off"
                >
                <button
                    type="button"
                    id="clearSearchButton"
                    class="{{ $currentSearchTerm !== '' ? '' : 'hidden' }} absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                    title="Clear search"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Records Per Page Dropdown --}}
            <div class="flex items-center space-x-2 shrink-0 self-end sm:self-auto">
                <label for="recordsPerPageSelect" class="text-xs font-medium text-slate-600 uppercase tracking-wider">Per Page:</label>
                <select
                    id="recordsPerPageSelect"
                    name="per_page"
                    class="border border-slate-300 rounded-lg py-2 px-3 text-sm font-medium text-slate-700 bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors cursor-pointer"
                >
                    <option value="10" @selected($selectedRecordsPerPage === 10)>10</option>
                    <option value="25" @selected($selectedRecordsPerPage === 25)>25</option>
                    <option value="50" @selected($selectedRecordsPerPage === 50)>50</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Donations Table Container (Dynamically updated via live search) --}}
    <div id="resultsTableContainer" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        @if ($paginatedDonationsList->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-4">Donation ID</th>
                            <th scope="col" class="py-3.5 px-4">Donation Date</th>
                            <th scope="col" class="py-3.5 px-4">Donor Name</th>
                            <th scope="col" class="py-3.5 px-4">Donor ID</th>
                            <th scope="col" class="py-3.5 px-4">Amount</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($paginatedDonationsList as $donationRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Donation ID --}}
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-slate-700">
                                    #{{ $donationRecord->Donation_ID }}
                                </td>

                                {{-- Formatted Donation Date --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-800">
                                    {{ $donationRecord->Donation_Date ? $donationRecord->Donation_Date->format('M j, Y') : '—' }}
                                </td>

                                {{-- Associated Donor Contact --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($donationRecord->contact)
                                        <a href="{{ route('people.show', $donationRecord->contact) }}" class="font-medium text-sky-700 hover:text-sky-900 hover:underline">
                                            {{ $donationRecord->contact->Last_Name }}, {{ $donationRecord->contact->First_Name }}
                                        </a>
                                        @if ($donationRecord->contact->Email_address)
                                            <span class="text-xs text-slate-400 block">{{ $donationRecord->contact->Email_address }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Unlinked Donor</span>
                                    @endif
                                </td>

                                {{-- Donor ID --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $donationRecord->Donor_ID ?? '—' }}
                                </td>

                                {{-- Formatted Donation Amount --}}
                                <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-emerald-700">
                                    ${{ number_format((float) $donationRecord->Donation_Amount, 2) }}
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-right text-xs">
                                    @if ($donationRecord->contact)
                                        <a href="{{ route('people.show', $donationRecord->contact) }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border border-slate-200/80 transition-colors" title="View donor member details and history">
                                            View Donor
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links Bar --}}
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $paginatedDonationsList->links() }}
            </div>
        @else
            {{-- Empty State when no results found --}}
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">No donations found</h3>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($currentSearchTerm !== '')
                        No donations match the search term "<span class="font-medium text-slate-800">{{ $currentSearchTerm }}</span>".
                    @else
                        There are currently no donations recorded in the database.
                    @endif
                </p>
                @if ($currentSearchTerm !== '')
                    <div class="mt-4">
                        <button type="button" id="emptyStateClearFilterButton" class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-medium bg-sky-600 text-white hover:bg-sky-700 shadow-xs transition-colors cursor-pointer">
                            Clear Search Filter
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

</div>

{{-- Vanilla JavaScript for Seamless Real-Time Live Search and Filter Submissions --}}
<script>
/* @ai-start: vanilla JavaScript handling seamless dynamic search as characters are typed or removed */
(function initializeDynamicDonationsSearch() {
    function setupDonationsSearchListeners() {
        const searchInputElement = document.getElementById('liveSearchInput');
        const recordsPerPageSelectElement = document.getElementById('recordsPerPageSelect');
        const donationsFilterFormElement = document.getElementById('donationsFilterForm');
        const clearSearchButtonElement = document.getElementById('clearSearchButton');
        const resultsTableContainerElement = document.getElementById('resultsTableContainer');
        const totalDonationsCountDisplayElement = document.getElementById('totalDonationsCountDisplay');

        if (!searchInputElement || !donationsFilterFormElement || !resultsTableContainerElement) {
            return;
        }

        let debounceTimerIdentifier = null;
        let activeAbortController = null;

        // Perform asynchronous table reload without disrupting user typing or losing input focus
        function executeDynamicSearchRequest(targetUrlOverride) {
            let requestUrl;

            if (targetUrlOverride) {
                requestUrl = new URL(targetUrlOverride, window.location.origin);
            } else {
                requestUrl = new URL(donationsFilterFormElement.action, window.location.origin);
                const currentSearchTerm = searchInputElement.value.trim();
                const selectedPerPage = recordsPerPageSelectElement ? recordsPerPageSelectElement.value : '10';

                if (currentSearchTerm !== '') {
                    requestUrl.searchParams.set('search', currentSearchTerm);
                }
                requestUrl.searchParams.set('per_page', selectedPerPage);
            }

            // Update clear button visibility
            if (clearSearchButtonElement) {
                if (searchInputElement.value.trim() !== '') {
                    clearSearchButtonElement.classList.remove('hidden');
                } else {
                    clearSearchButtonElement.classList.add('hidden');
                }
            }

            // Cancel any pending fetch request
            if (activeAbortController) {
                activeAbortController.abort();
            }
            activeAbortController = new AbortController();

            // Synchronize browser history and address bar URL
            window.history.replaceState(null, '', requestUrl.toString());

            fetch(requestUrl.toString(), {
                signal: activeAbortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (httpResponse) {
                return httpResponse.text();
            })
            .then(function (responseHtml) {
                const parsedDom = new DOMParser().parseFromString(responseHtml, 'text/html');
                const freshResultsContainer = parsedDom.getElementById('resultsTableContainer');
                const freshTotalCount = parsedDom.getElementById('totalDonationsCountDisplay');

                if (freshResultsContainer && resultsTableContainerElement) {
                    resultsTableContainerElement.innerHTML = freshResultsContainer.innerHTML;
                }

                if (freshTotalCount && totalDonationsCountDisplayElement) {
                    totalDonationsCountDisplayElement.innerHTML = freshTotalCount.innerHTML;
                }
            })
            .catch(function (fetchError) {
                if (fetchError.name !== 'AbortError') {
                    console.error('Dynamic search fetch error:', fetchError);
                }
            });
        }

        // Live input event: fires on every keystroke as characters are added or removed
        searchInputElement.addEventListener('input', function () {
            clearTimeout(debounceTimerIdentifier);
            debounceTimerIdentifier = setTimeout(function () {
                executeDynamicSearchRequest();
            }, 250);
        });

        // Enter key submission: search immediately without waiting for debounce
        donationsFilterFormElement.addEventListener('submit', function (submissionEvent) {
            submissionEvent.preventDefault();
            clearTimeout(debounceTimerIdentifier);
            executeDynamicSearchRequest();
        });

        // Records per page change: update results immediately
        if (recordsPerPageSelectElement) {
            recordsPerPageSelectElement.addEventListener('change', function () {
                clearTimeout(debounceTimerIdentifier);
                executeDynamicSearchRequest();
            });
        }

        // Clear search button in input field
        if (clearSearchButtonElement) {
            clearSearchButtonElement.addEventListener('click', function () {
                searchInputElement.value = '';
                searchInputElement.focus();
                executeDynamicSearchRequest();
            });
        }

        // Intercept pagination clicks inside the table to reload dynamically
        resultsTableContainerElement.addEventListener('click', function (clickEvent) {
            const emptyStateClearButton = clickEvent.target.closest('#emptyStateClearFilterButton');
            if (emptyStateClearButton) {
                clickEvent.preventDefault();
                searchInputElement.value = '';
                searchInputElement.focus();
                executeDynamicSearchRequest();
                return;
            }

            const paginationLinkElement = clickEvent.target.closest('nav a, .pagination a');
            if (paginationLinkElement && paginationLinkElement.href) {
                clickEvent.preventDefault();
                executeDynamicSearchRequest(paginationLinkElement.href);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupDonationsSearchListeners);
    } else {
        setupDonationsSearchListeners();
    }
})();
/* @ai-end */
</script>
@endsection
{{-- @ai-end --}}
