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
                    <p class="text-xs text-slate-500">Clean SaaS workspace</p>
                </div>
            </a>

            <form class="shell-nav-search" method="GET" action="{{ route('dashboard') }}">
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
                <div class="hidden xl:block text-right">
                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 text-sm font-semibold text-primary-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
