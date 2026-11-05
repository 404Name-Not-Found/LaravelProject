{{-- @ai-start: view template for the people directory listing with dynamic live search, calm trustworthy styling, and pagination --}}
@extends('layouts.app')

@section('title', 'People Directory')

@section('content')
<div class="space-y-6">

    {{-- Page Header Section --}}
    <div class="relative flex flex-col sm:flex-row items-center justify-between sm:justify-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 text-center">People Directory</h1>
        <div class="sm:absolute sm:right-0 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg px-4 py-2 shadow-xs self-center">
            Total Members: <span id="totalMembersCountDisplay" class="font-semibold text-slate-900">{{ number_format($paginatedContactsList->total()) }}</span>
        </div>
    </div>

    {{-- Controls Bar: Live Search Box and Records Per Page Selector --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('people.index') }}" id="peopleFilterForm" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
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
                    placeholder="Search people..."
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

    {{-- People Table Container (Dynamically updated via live search) --}}
    <div id="resultsTableContainer" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        @if ($paginatedContactsList->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-4">Name</th>
                            <th scope="col" class="py-3.5 px-4">Contact Info</th>
                            <th scope="col" class="py-3.5 px-4">Status & Position</th>
                            <th scope="col" class="py-3.5 px-4">Household</th>
                            <th scope="col" class="py-3.5 px-4">Date of Birth</th>
                            <th scope="col" class="py-3.5 px-4">Donor ID</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($paginatedContactsList as $contactRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Contact Full Name --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <a href="{{ route('people.show', $contactRecord) }}" class="font-medium text-sky-700 hover:text-sky-900 hover:underline">
                                        {{ $contactRecord->Last_Name }}, {{ $contactRecord->First_Name }}
                                    </a>
                                    @if ($contactRecord->Gender)
                                        <span class="text-xs text-slate-400 block">{{ $contactRecord->Gender }}</span>
                                    @endif
                                </td>

                                {{-- Contact Information: Email and Mobile Phone --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    <div>{{ $contactRecord->Email_address ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $contactRecord->Mobile_Phone ?? '—' }}</div>
                                </td>

                                {{-- Contact Status & Household Position --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ strcasecmp($contactRecord->Contact_Status, 'Active') === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $contactRecord->Contact_Status ?? 'Active' }}
                                    </span>
                                    @if ($contactRecord->Household_Position)
                                        <span class="text-xs text-slate-500 block mt-0.5">{{ $contactRecord->Household_Position }}</span>
                                    @endif
                                </td>

                                {{-- Associated Household --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    @if ($contactRecord->household)
                                        <div class="font-medium text-slate-800">{{ $contactRecord->household->Household_Name }}</div>
                                        <div class="text-xs text-slate-500">{{ $contactRecord->household->City ?? '' }} {{ $contactRecord->household->State ?? '' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">None</span>
                                    @endif
                                </td>

                                {{-- Formatted Date of Birth --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $contactRecord->Date_of_Birth ? $contactRecord->Date_of_Birth->format('M j, Y') : '—' }}
                                </td>

                                {{-- Donor ID --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-mono text-xs">
                                    @if ($contactRecord->Donor_ID)
                                        <span class="bg-slate-100 px-2 py-1 rounded-md text-slate-700">#{{ $contactRecord->Donor_ID }}</span>
                                    @else
                                        <span class="text-slate-400 italic">None</span>
                                    @endif
                                </td>

                                {{-- Action Buttons with Clear Semantic Intent --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-right text-xs">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        {{-- Informational View Button --}}
                                        <a href="{{ route('people.show', $contactRecord) }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border border-slate-200/80 transition-colors" title="View details and history">
                                            View
                                        </a>
                                        {{-- Calm Soft Blue Register Button (triggers pop-up modal) --}}
                                        <button
                                            type="button"
                                            class="open-registration-modal-button px-2.5 py-1.5 rounded-md bg-sky-50 hover:bg-sky-100 text-sky-700 font-medium border border-sky-200/80 transition-colors cursor-pointer"
                                            data-contact-id="{{ $contactRecord->Contact_ID }}"
                                            data-contact-name="{{ $contactRecord->First_Name }} {{ $contactRecord->Last_Name }}"
                                            title="Register for an event"
                                        >
                                            Register
                                        </button>
                                        {{-- Purposeful Emerald Green Donate Button (triggers pop-up modal) --}}
                                        <button
                                            type="button"
                                            class="open-donation-modal-button px-2.5 py-1.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-medium border border-emerald-200/80 transition-colors cursor-pointer"
                                            data-contact-id="{{ $contactRecord->Contact_ID }}"
                                            data-contact-name="{{ $contactRecord->First_Name }} {{ $contactRecord->Last_Name }}"
                                            title="Make a donation"
                                        >
                                            Donate
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links Bar --}}
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $paginatedContactsList->links() }}
            </div>
        @else
            {{-- Empty State when no results found --}}
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">No people found</h3>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($currentSearchTerm !== '')
                        No contacts match the search term "<span class="font-medium text-slate-800">{{ $currentSearchTerm }}</span>".
                    @else
                        There are currently no contacts in the database.
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

    {{-- Event Registration Pop-Up Modal Component --}}
    @include('registrations.modal', ['availableEventsList' => $availableEventsList])

    {{-- Donation Pop-Up Modal Component --}}
    @include('donations.modal')

</div>

{{-- Vanilla JavaScript for Seamless Real-Time Live Search and Filter Submissions --}}
<script>
/* @ai-start: vanilla JavaScript handling seamless dynamic search as characters are typed or removed */
(function initializeDynamicPeopleSearch() {
    function setupSearchListeners() {
        const searchInputElement = document.getElementById('liveSearchInput');
        const recordsPerPageSelectElement = document.getElementById('recordsPerPageSelect');
        const peopleFilterFormElement = document.getElementById('peopleFilterForm');
        const clearSearchButtonElement = document.getElementById('clearSearchButton');
        const resultsTableContainerElement = document.getElementById('resultsTableContainer');
        const totalMembersCountDisplayElement = document.getElementById('totalMembersCountDisplay');

        if (!searchInputElement || !peopleFilterFormElement || !resultsTableContainerElement) {
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
                requestUrl = new URL(peopleFilterFormElement.action, window.location.origin);
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
                const freshTotalCount = parsedDom.getElementById('totalMembersCountDisplay');

                if (freshResultsContainer && resultsTableContainerElement) {
                    resultsTableContainerElement.innerHTML = freshResultsContainer.innerHTML;
                }

                if (freshTotalCount && totalMembersCountDisplayElement) {
                    totalMembersCountDisplayElement.innerHTML = freshTotalCount.innerHTML;
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
        peopleFilterFormElement.addEventListener('submit', function (submissionEvent) {
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
        document.addEventListener('DOMContentLoaded', setupSearchListeners);
    } else {
        setupSearchListeners();
    }
})();
/* @ai-end */
</script>
@endsection
{{-- @ai-end --}}
