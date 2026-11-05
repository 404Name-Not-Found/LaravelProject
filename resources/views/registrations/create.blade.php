{{-- @ai-start: view template for the event registration form allowing selection of an event for a contact --}}
@extends('layouts.app')

@section('title', 'Register ' . $targetContactRecord->First_Name . ' ' . $targetContactRecord->Last_Name . ' for an Event')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Breadcrumb & Back Link --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('people.show', $targetContactRecord) }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-700 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Member Profile
        </a>
    </div>

    {{-- Form Container Card --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs">
        {{-- Card Header --}}
        <div class="border-b border-slate-100 pb-5">
            <h1 class="text-2xl font-bold text-slate-900">Event Registration</h1>
        </div>

        {{-- Member Information Summary --}}
        <div class="my-6 p-4 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm shrink-0">
                {{ substr($targetContactRecord->First_Name, 0, 1) }}{{ substr($targetContactRecord->Last_Name, 0, 1) }}
            </div>
            <div class="text-sm">
                <div class="font-semibold text-slate-900">
                    {{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}
                </div>
                <div class="text-xs text-slate-500">
                    {{ $targetContactRecord->Email_address ?? 'No email on file' }}
                    @if ($targetContactRecord->household)
                        &middot; {{ $targetContactRecord->household->Household_Name }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Registration Form --}}
        <form method="POST" action="{{ route('people.registrations.store', $targetContactRecord) }}" class="space-y-6">
            @csrf

            {{-- Event Selection Field --}}
            <div>
                <label for="eventSelectionInput" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Select Event <span class="text-rose-500">*</span>
                </label>

                @if ($availableEventsList->count() > 0)
                    <select
                        id="eventSelectionInput"
                        name="Event_ID"
                        class="block w-full border @error('Event_ID') border-rose-300 ring-rose-200 @else border-slate-300 @enderror rounded-lg py-2.5 px-3 text-sm font-medium text-slate-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors cursor-pointer"
                        required
                    >
                        <option value="">-- Choose an event --</option>
                        @foreach ($availableEventsList as $eventOption)
                            @php
                                $isContactAlreadyRegistered = in_array($eventOption->Event_ID, $alreadyRegisteredEventIdsList, true);
                            @endphp
                            <option
                                value="{{ $eventOption->Event_ID }}"
                                @disabled($isContactAlreadyRegistered)
                                @selected((int) old('Event_ID') === $eventOption->Event_ID)
                            >
                                {{ $eventOption->Event_Title }}
                                ({{ $eventOption->Event_Start_Date ? $eventOption->Event_Start_Date->format('M j, Y g:i A') : 'No date' }})
                                @if ($isContactAlreadyRegistered)
                                    — [Already Registered]
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('Event_ID')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                @else
                    <p class="text-sm text-slate-500 italic">No events are currently available to register for.</p>
                @endif
            </div>

            {{-- Form Submission Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a
                    href="{{ route('people.index') }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200/80 transition-colors"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    @disabled($availableEventsList->count() === 0)
                    class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 disabled:bg-slate-300 disabled:cursor-not-allowed shadow-xs transition-colors"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm Registration
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
{{-- @ai-end --}}
