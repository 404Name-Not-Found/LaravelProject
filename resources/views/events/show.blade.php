{{-- @ai-start: view template displaying detailed event information and registered participants roster --}}
@extends('layouts.app')

@section('title', 'Event Details - ' . $targetEventRecord->Event_Title)

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb & Back Link --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-sky-700 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Events Directory
        </a>
    </div>

    {{-- Event Header and Information Card --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center justify-center p-2 rounded-lg bg-sky-100 text-sky-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $targetEventRecord->Event_Title }}</h1>
                </div>
                <div class="text-sm text-slate-500 flex flex-wrap items-center gap-x-4 gap-y-1 pt-1">
                    <div>
                        <span class="font-medium text-slate-700">Starts:</span>
                        {{ $targetEventRecord->Event_Start_Date ? $targetEventRecord->Event_Start_Date->format('M j, Y g:i A') : '—' }}
                    </div>
                    <div>
                        <span class="font-medium text-slate-700">Ends:</span>
                        {{ $targetEventRecord->Event_End_Date ? $targetEventRecord->Event_End_Date->format('M j, Y g:i A') : '—' }}
                    </div>
                </div>
            </div>

            {{-- Registered Participants Badge --}}
            <div class="text-sm bg-sky-50 border border-sky-200 rounded-lg px-4 py-2 text-sky-800 font-medium self-start md:self-auto">
                Total Registered: <span class="font-bold text-sky-950">{{ $targetEventRecord->contacts->count() }}</span>
            </div>
        </div>

        {{-- Event Description Body --}}
        <div class="mt-6 pt-6 border-t border-slate-100 text-sm text-slate-700 leading-relaxed">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Description</h2>
            <p class="whitespace-pre-line">{{ $targetEventRecord->Event_Description ?? 'No additional description provided for this event.' }}</p>
        </div>
    </div>

    {{-- Registered Participants Roster Section --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Registered Participants Roster</h2>
            </div>
            <a href="{{ route('people.index') }}" class="text-xs font-medium text-sky-700 hover:text-sky-800">
                Browse People to Register &rarr;
            </a>
        </div>

        @if ($targetEventRecord->contacts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="py-3 px-4">Participant Name</th>
                            <th scope="col" class="py-3 px-4">Contact Info</th>
                            <th scope="col" class="py-3 px-4">Household & Position</th>
                            <th scope="col" class="py-3 px-4">Status</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($targetEventRecord->contacts as $contactRecord)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <a href="{{ route('people.show', $contactRecord) }}" class="font-medium text-sky-700 hover:text-sky-900 hover:underline">
                                        {{ $contactRecord->Last_Name }}, {{ $contactRecord->First_Name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 text-xs">
                                    <div>{{ $contactRecord->Email_address ?? '—' }}</div>
                                    <div class="text-slate-400">{{ $contactRecord->Mobile_Phone ?? '—' }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 text-xs">
                                    @if ($contactRecord->household)
                                        <div class="font-medium text-slate-800">{{ $contactRecord->household->Household_Name }}</div>
                                        <div class="text-slate-400">{{ $contactRecord->Household_Position ?? 'Member' }}</div>
                                    @else
                                        <span class="text-slate-400 italic">None</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ strcasecmp($contactRecord->Contact_Status, 'Active') === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $contactRecord->Contact_Status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-right text-xs">
                                    <a href="{{ route('people.show', $contactRecord) }}" class="px-2.5 py-1.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border border-slate-200/80 transition-colors">
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-sm text-slate-500">
                <p>No participants have registered for this event yet.</p>
                <div class="mt-3">
                    <a href="{{ route('people.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200/80 transition-colors">
                        Go to People Directory to Register Members
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
{{-- @ai-end --}}
