{{-- @ai-start: base layout template with GenevaTrac navigation, calm soft blue branding, and flash messages --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GenevaTrac') - GenevaTrac</title>

    {{-- Fonts and compiled Tailwind CSS/JS assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col bg-slate-50/70 text-slate-800 antialiased">

    {{-- Main navigation header with calm trustworthy styling --}}
    <header class="bg-white/95 backdrop-blur-xs border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Brand and primary navigation links --}}
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-sky-700 hover:text-sky-800 transition-colors flex items-center gap-2.5">
                        {{-- GenevaTrac simple church sanctuary logo --}}
                        <svg class="h-8 w-8 text-sky-600 shrink-0" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect width="32" height="32" rx="8" class="fill-sky-100" />
                            <path d="M16 6L23 13V24C23 24.55 22.55 25 22 25H10C9.45 25 9 24.55 9 24V13L16 6Z" class="fill-sky-600" />
                            <path d="M16 4V10M13.5 6.5H18.5" stroke="#0369A1" stroke-width="1.75" stroke-linecap="round" />
                            <path d="M13.5 25V17C13.5 15.62 14.62 14.5 16 14.5C17.38 14.5 18.5 15.62 18.5 17V25" class="fill-sky-100" />
                        </svg>
                        <span>GenevaTrac</span>
                    </a>
                    <nav class="flex space-x-2">
                        <a href="{{ url('/people') }}"
                           class="px-3 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->is('people*') ? 'border-sky-600 text-sky-700 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 rounded-md' }}">
                            People
                        </a>
                        <a href="{{ url('/events') }}"
                           class="px-3 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->is('events*') ? 'border-sky-600 text-sky-700 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 rounded-md' }}">
                            Events
                        </a>
                        <a href="{{ url('/donations') }}"
                           class="px-3 py-2 text-sm font-medium transition-colors border-b-2 {{ request()->is('donations*') ? 'border-sky-600 text-sky-700 font-semibold' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 rounded-md' }}">
                            Donations
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    {{-- Main content region --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Validation error messages display --}}
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 shadow-xs">
                <div class="flex items-center gap-2 font-semibold">
                    <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $validationErrorMessage)
                        <li>{{ $validationErrorMessage }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Flash message display for feedback --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center shadow-xs">
                <svg class="h-5 w-5 text-emerald-600 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800 flex items-center shadow-xs">
                <svg class="h-5 w-5 text-rose-600 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Page specific body --}}
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            GenevaTrac &copy; {{ date('Y') }} &middot; Church Management System
        </div>
    </footer>

</body>
</html>
{{-- @ai-end --}}
