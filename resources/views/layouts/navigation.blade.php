<nav class="shell-nav">
    <div class="page-container">
        <div class="shell-nav-inner">
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 lg:hidden"
                @click="sidebarOpen = true"
            >
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-9 w-9 rounded-2xl bg-primary-50 p-2 text-primary-600" />
                <div class="hidden sm:block">
                    <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Laravel') }}</p>
                    <p class="text-xs text-slate-500">Your Complete Personal Organizer</p>
                </div>
            </a>

            <form class="shell-nav-search" method="GET" action="{{ route('search') }}">
                <label for="global-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.4a7.2 7.2 0 1 1-14.4 0 7.2 7.2 0 0 1 14.4 0Z" />
                        </svg>
                    </span>
                    <input
                        id="global-search"
                        type="search"
                        name="q"
                        placeholder="Search tasks, expenses, notes..."
                        class="form-input w-full bg-slate-50 pl-10"
                    />
                </div>
            </form>

            <div class="ms-auto flex items-center gap-3">
                @php
                    $monthlyBudgetAlertCount = auth()->user()->budgets()
                        ->active()
                        ->where('month_year', now()->format('Y-m'))
                        ->whereNotNull('alert_sent_at')
                        ->count();
                @endphp

                @if ($monthlyBudgetAlertCount > 0)
                    <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        <span class="hidden lg:inline">Budget alert</span>
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-700">{{ $monthlyBudgetAlertCount }}</span>
                    </span>
                @endif
                
            </div>
        </div>
    </div>
</nav>
