{{-- @ai-start: view template displaying detailed profile, household information, event participation, and donation history with calm trustworthy colors --}}
@extends('layouts.app')

@section('title', 'Member Profile - ' . $targetContactRecord->First_Name . ' ' . $targetContactRecord->Last_Name)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb & Top Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('people.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-700 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to People Directory
        </a>
    </div>

    {{-- Profile Hero Card with Action Buttons --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold uppercase shrink-0">
                {{ substr($targetContactRecord->First_Name, 0, 1) }}{{ substr($targetContactRecord->Last_Name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ strcasecmp($targetContactRecord->Contact_Status, 'Active') === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ $targetContactRecord->Contact_Status ?? 'Active' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-0.5">
                    Contact ID: #{{ $targetContactRecord->Contact_ID }} &middot;
                    @if ($targetContactRecord->Donor_ID)
                        Donor ID: <span class="font-mono font-medium text-slate-700">#{{ $targetContactRecord->Donor_ID }}</span>
                    @else
                        <span class="italic text-slate-400">No Donor ID Assigned</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Primary Action Buttons: Register for Event & Make a Donation --}}
        <div class="flex items-center space-x-3 shrink-0">
            <button
                type="button"
                class="open-registration-modal-button inline-flex items-center px-4 py-2 border border-sky-300 rounded-lg text-sm font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 transition-colors shadow-xs cursor-pointer"
                data-contact-id="{{ $targetContactRecord->Contact_ID }}"
                data-contact-name="{{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Register for Event
            </button>
            <button
                type="button"
                class="open-donation-modal-button inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-xs cursor-pointer"
                data-contact-id="{{ $targetContactRecord->Contact_ID }}"
                data-contact-name="{{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Make a Donation
            </button>
        </div>
    </div>

    {{-- Details Grid: Personal Information and Household / Contact Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Personal Details Card --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Personal Information</h2>
            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">First Name</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->First_Name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Last Name</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Last_Name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Date of Birth</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $targetContactRecord->Date_of_Birth ? $targetContactRecord->Date_of_Birth->format('M j, Y') : '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Gender</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Gender ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Marital Status</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Marital_Status ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Status</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Contact_Status ?? 'Active' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Contact & Household Card --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Contact & Household</h2>
            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Email Address</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Email_address ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Mobile Phone</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Mobile_Phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Household Name</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        {{ $targetContactRecord->household->Household_Name ?? 'None' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-slate-500 uppercase">Household Position</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $targetContactRecord->Household_Position ?? '—' }}</dd>
                </div>
                @if ($targetContactRecord->household)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-slate-500 uppercase">Address</dt>
                        <dd class="mt-1 font-medium text-slate-800">
                            {{ $targetContactRecord->household->Address_Line_1 ?? '' }}<br>
                            {{ $targetContactRecord->household->City ?? '' }}, {{ $targetContactRecord->household->State ?? '' }} {{ $targetContactRecord->household->Zip_Code ?? '' }}
                            @if ($targetContactRecord->household->Home_Phone)
                                <span class="block text-xs text-slate-500 mt-1">Home Phone: {{ $targetContactRecord->household->Home_Phone }}</span>
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

    </div>

    {{-- Event Participation History Section --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Event History</h2>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 text-sky-800 border border-sky-100">
                {{ $targetContactRecord->events->count() }} Registered
            </span>
        </div>

        @if ($targetContactRecord->events->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3 px-4">Event Title</th>
                            <th scope="col" class="py-3 px-4">Start Date & Time</th>
                            <th scope="col" class="py-3 px-4">End Date & Time</th>
                            <th scope="col" class="py-3 px-4">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($targetContactRecord->events as $eventRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $eventRecord->Event_Title }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $eventRecord->Event_Start_Date ? $eventRecord->Event_Start_Date->format('M j, Y g:i A') : '—' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $eventRecord->Event_End_Date ? $eventRecord->Event_End_Date->format('M j, Y g:i A') : '—' }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 text-xs max-w-md truncate">
                                    {{ $eventRecord->Event_Description ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-sm text-slate-500">
                <p>No event registrations recorded for this contact.</p>
                <div class="mt-3">
                    <button
                        type="button"
                        class="open-registration-modal-button inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200/80 transition-colors cursor-pointer"
                        data-contact-id="{{ $targetContactRecord->Contact_ID }}"
                        data-contact-name="{{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}"
                    >
                        Register for an Event
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Donation and Financial Contributions History Section --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Donation History</h2>
            </div>
            @php
                $calculatedTotalDonationsAmount = $targetContactRecord->donations->sum('Donation_Amount');
            @endphp
            <div class="text-sm bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5 text-emerald-800 font-medium self-start sm:self-auto">
                Lifetime Total: <span class="font-bold">${{ number_format($calculatedTotalDonationsAmount, 2) }}</span>
                <span class="text-xs text-emerald-600 ml-1">({{ $targetContactRecord->donations->count() }} contributions)</span>
            </div>
        </div>

        @if ($targetContactRecord->donations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3 px-4">Donation Date</th>
                            <th scope="col" class="py-3 px-4">Amount</th>
                            <th scope="col" class="py-3 px-4">Donation ID</th>
                            <th scope="col" class="py-3 px-4">Donor ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($targetContactRecord->donations as $donationRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-medium text-slate-800 whitespace-nowrap">
                                    {{ $donationRecord->Donation_Date ? $donationRecord->Donation_Date->format('M j, Y') : '—' }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-emerald-700 whitespace-nowrap">
                                    ${{ number_format((float) $donationRecord->Donation_Amount, 2) }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                    #{{ $donationRecord->Donation_ID }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                    #{{ $donationRecord->Donor_ID }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-sm text-slate-500">
                <p>No donations recorded for this contact.</p>
                <div class="mt-3">
                    <button
                        type="button"
                        class="open-donation-modal-button inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition-colors cursor-pointer"
                        data-contact-id="{{ $targetContactRecord->Contact_ID }}"
                        data-contact-name="{{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}"
                    >
                        Make a Donation
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Event Registration Pop-Up Modal Component --}}
    @include('registrations.modal', ['availableEventsList' => $availableEventsList])

    {{-- Donation Pop-Up Modal Component --}}
    @include('donations.modal')

</div>
@endsection
{{-- @ai-end --}}
