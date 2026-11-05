{{-- @ai-start: view template for the standalone donation form allowing entry of amount and date for a contact --}}
@extends('layouts.app')

@section('title', 'Make a Donation - ' . $targetContactRecord->First_Name . ' ' . $targetContactRecord->Last_Name)

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
            <h1 class="text-2xl font-bold text-slate-900">Make a Donation</h1>
        </div>

        {{-- Member Information Summary --}}
        <div class="my-6 p-4 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center space-x-4">
            <div class="h-12 w-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm shrink-0">
                {{ substr($targetContactRecord->First_Name, 0, 1) }}{{ substr($targetContactRecord->Last_Name, 0, 1) }}
            </div>
            <div class="text-sm">
                <div class="font-semibold text-slate-900">
                    {{ $targetContactRecord->First_Name }} {{ $targetContactRecord->Last_Name }}
                </div>
                <div class="text-xs text-slate-500">
                    @if ($targetContactRecord->Donor_ID)
                        Donor ID: <span class="font-mono font-medium text-slate-700">#{{ $targetContactRecord->Donor_ID }}</span> &middot;
                    @else
                        <span class="italic text-slate-400">First-time donor (Donor ID will be auto-assigned)</span> &middot;
                    @endif
                    {{ $targetContactRecord->Email_address ?? 'No email on file' }}
                </div>
            </div>
        </div>

        {{-- Donation Form --}}
        <form method="POST" action="{{ route('people.donations.store', $targetContactRecord) }}" class="space-y-6">
            @csrf

            {{-- Donation Amount Input Field --}}
            <div>
                <label for="donationAmountInput" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Donation Amount ($) <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-lg shadow-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-slate-500 text-sm font-medium">$</span>
                    </div>
                    <input
                        type="number"
                        name="Donation_Amount"
                        id="donationAmountInput"
                        step="0.01"
                        min="0.01"
                        max="9999999.99"
                        value="{{ old('Donation_Amount') }}"
                        placeholder="0.00"
                        required
                        class="block w-full rounded-lg border @error('Donation_Amount') border-rose-300 ring-rose-200 @else border-slate-300 @enderror pl-7 pr-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                    >
                </div>
                @error('Donation_Amount')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Donation Date Input Field --}}
            <div>
                <label for="donationDateInput" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Donation Date <span class="text-rose-500">*</span>
                </label>
                <input
                    type="date"
                    name="Donation_Date"
                    id="donationDateInput"
                    value="{{ old('Donation_Date', date('Y-m-d')) }}"
                    required
                    class="block w-full rounded-lg border @error('Donation_Date') border-rose-300 ring-rose-200 @else border-slate-300 @enderror px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer"
                >
                @error('Donation_Date')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Submission Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('people.show', $targetContactRecord) }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition-colors cursor-pointer"
                >
                    Save Donation
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
{{-- @ai-end --}}
