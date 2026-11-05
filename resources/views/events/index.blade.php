{{-- @ai-start: view template for the events directory listing with dynamic live search, date formatting, and pagination --}}
@extends('layouts.app')

@section('title', 'Events Directory')

@section('content')
<div class="space-y-6">

    {{-- Page Header Section --}}
    <div class="relative flex flex-col sm:flex-row items-center justify-between sm:justify-center gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 text-center">Events Directory</h1>
        <div class="sm:absolute sm:right-0 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg px-4 py-2 shadow-xs self-center">
            Total Events: <span id="totalEventsCountDisplay" class="font-semibold text-slate-900">{{ number_format($paginatedEventsList->total()) }}</span>
        </div>
    </div>

    {{-- Controls Bar: Live Search Box and Records Per Page Selector --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('events.index') }}" id="eventsFilterForm" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
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
                    placeholder="Search events..."
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

    {{-- Events Table Container (Dynamically updated via live search) --}}
    <div id="resultsTableContainer" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        @if ($paginatedEventsList->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3.5 px-4">Event Title</th>
                            <th scope="col" class="py-3.5 px-4">Start Date & Time</th>
                            <th scope="col" class="py-3.5 px-4">End Date & Time</th>
                            <th scope="col" class="py-3.5 px-4">Description</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Registered</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($paginatedEventsList as $eventRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                {{-- Event Title --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <a href="{{ route('events.show', $eventRecord) }}" class="font-medium text-sky-700 hover:text-sky-900 hover:underline">
                                        {{ $eventRecord->Event_Title }}
                                    </a>
                                </td>

                                {{-- Formatted Start Date & Time --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $eventRecord->Event_Start_Date ? $eventRecord->Event_Start_Date->format('M j, Y g:i A') : '—' }}
                                </td>

                                {{-- Formatted End Date & Time --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                    {{ $eventRecord->Event_End_Date ? $eventRecord->Event_End_Date->format('M j, Y g:i A') : '—' }}
                                </td>

                                {{-- Event Description Snippet --}}
                                <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate text-xs">
                                    {{ $eventRecord->Event_Description ?? 'No description provided.' }}
                                </td>

                                {{-- Attendees Count Badge --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-800 border border-sky-100">
                                        {{ $eventRecord->contacts_count }} {{ Str::plural('attendee', $eventRecord->contacts_count) }}
                                    </span>
                                </td>

                                {{-- Action Button --}}
                                <td class="py-3.5 px-4 whitespace-nowrap text-right text-xs">
                                    <a href="{{ route('events.show', $eventRecord) }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border border-slate-200/80 transition-colors" title="View event details and attendees">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links Bar --}}
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $paginatedEventsList->links() }}
            </div>
        @else
            {{-- Empty State when no results found --}}
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">No events found</h3>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($currentSearchTerm !== '')
                        No events match the search term "<span class="font-medium text-slate-800">{{ $currentSearchTerm }}</span>".
                    @else
                        There are currently no events scheduled in the database.
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
(function initializeDynamicEventsSearch() {
    function setupEventsSearchListeners() {
        const searchInputElement = document.getElementById('liveSearchInput');
        const recordsPerPageSelectElement = document.getElementById('recordsPerPageSelect');
        const eventsFilterFormElement = document.getElementById('eventsFilterForm');
        const clearSearchButtonElement = document.getElementById('clearSearchButton');
        const resultsTableContainerElement = document.getElementById('resultsTableContainer');
        const totalEventsCountDisplayElement = document.getElementById('totalEventsCountDisplay');

        if (!searchInputElement || !eventsFilterFormElement || !resultsTableContainerElement) {
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
                requestUrl = new URL(eventsFilterFormElement.action, window.location.origin);
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
                const freshTotalCount = parsedDom.getElementById('totalEventsCountDisplay');

                if (freshResultsContainer && resultsTableContainerElement) {
                    resultsTableContainerElement.innerHTML = freshResultsContainer.innerHTML;
                }

                if (freshTotalCount && totalEventsCountDisplayElement) {
                    totalEventsCountDisplayElement.innerHTML = freshTotalCount.innerHTML;
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
        eventsFilterFormElement.addEventListener('submit', function (submissionEvent) {
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
        document.addEventListener('DOMContentLoaded', setupEventsSearchListeners);
    } else {
        setupEventsSearchListeners();
    }
})();
/* @ai-end */
</script>
@endsection
{{-- @ai-end --}}
