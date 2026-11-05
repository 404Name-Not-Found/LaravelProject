{{-- @ai-start: landing page template providing application overview, metrics, and navigation --}}
@extends('layouts.app')

@section('title', 'Welcome to GenevaTrac')

@section('content')
<div class="space-y-10">

    {{-- Hero Introduction Section with Background Image --}}
    <div class="relative overflow-hidden rounded-2xl bg-slate-900 px-6 py-16 sm:px-12 sm:py-24 text-white shadow-md flex items-center justify-center text-center" style="background-image: url('{{ asset('oldmain-web.jpg') }}'); background-size: cover; background-position: center;">
        {{-- Subtle dark overlay for contrast and text clarity --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-2xs"></div>

        <div class="relative z-10 w-full max-w-3xl flex flex-col items-center justify-center text-center">
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white drop-shadow-sm text-center">
                Welcome to GenevaTrac
            </h1>
        </div>
    </div>

    {{-- High-Level Church Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Total Contacts Metric Card --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center shrink-0 border border-sky-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ number_format($totalRegisteredContactsCount) }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Members</div>
            </div>
        </div>

        {{-- Total Events Metric Card --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ number_format($totalScheduledEventsCount) }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Events</div>
            </div>
        </div>

        {{-- Total Donations Metric Card --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">${{ number_format($totalContributedAmountSum, 2) }}</div>
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Donations</div>
            </div>
        </div>
    </div>

    {{-- Three Core Modules Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- People Module Card --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col justify-between shadow-xs hover:border-sky-300 transition-colors">
            <div class="space-y-3">
                <div class="inline-flex p-3 rounded-lg bg-sky-50 text-sky-700 border border-sky-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">People</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Members, households, and contact details.
                </p>
            </div>
            <div class="pt-6">
                <a href="{{ route('people.index') }}" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-semibold bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200/80 transition-colors">
                    Browse People &rarr;
                </a>
            </div>
        </div>

        {{-- Events Module Card --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col justify-between shadow-xs hover:border-amber-300 transition-colors">
            <div class="space-y-3">
                <div class="inline-flex p-3 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Events</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Schedules, dates, and attendee rosters.
                </p>
            </div>
            <div class="pt-6">
                <a href="{{ route('events.index') }}" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200/80 transition-colors">
                    Browse Events &rarr;
                </a>
            </div>
        </div>

        {{-- Donations Module Card --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col justify-between shadow-xs hover:border-emerald-300 transition-colors">
            <div class="space-y-3">
                <div class="inline-flex p-3 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Donations</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Contributions, giving records, and history.
                </p>
            </div>
            <div class="pt-6">
                <a href="{{ route('donations.index') }}" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200/80 transition-colors">
                    Browse Donations &rarr;
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Events Preview Table (if events exist) --}}
    @if ($upcomingEventsPreviewList->count() > 0)
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Events</h2>
                </div>
                <a href="{{ route('events.index') }}" class="text-xs font-semibold text-sky-700 hover:text-sky-900 hover:underline">
                    View All &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($upcomingEventsPreviewList as $previewEvent)
                    <div class="px-6 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50/80 transition-colors">
                        <div>
                            <a href="{{ route('events.show', $previewEvent) }}" class="font-semibold text-sm text-slate-900 hover:text-sky-700">
                                {{ $previewEvent->Event_Title }}
                            </a>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $previewEvent->Event_Start_Date ? $previewEvent->Event_Start_Date->format('M j, Y g:i A') : 'Date TBA' }}
                            </div>
                        </div>
                        <a href="{{ route('events.show', $previewEvent) }}" class="text-xs font-medium text-slate-600 hover:text-slate-900 px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 transition-colors self-start sm:self-auto">
                            View &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
{{-- @ai-end --}}
