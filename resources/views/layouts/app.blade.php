<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Digital Life Manager') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="shell-app">
            <div x-cloak class="fixed right-4 top-4 z-[60] w-[22rem] space-y-3">
                <template x-for="toast in $store.notifications.toasts" :key="toast.id">
                    <div
                        x-show="true"
                        x-transition.opacity.duration.200ms
                        class="rounded-2xl border bg-white p-4 shadow-2xl ring-1"
                        :class="toast.type === 'error' ? 'border-red-200 ring-red-100' : 'border-emerald-200 ring-emerald-100'"
                    >
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full" :class="toast.type === 'error' ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'">
                                <svg x-show="toast.type !== 'error'" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.296a1 1 0 0 1 0 1.414l-7.071 7.071a1 1 0 0 1-1.414 0L3.296 9.858A1 1 0 1 1 4.71 8.444l3.212 3.212 6.364-6.364a1 1 0 0 1 1.414 0Z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="toast.type === 'error'" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.22 7.03a.75.75 0 0 1 1.06 0L10 7.75l.72-.72a.75.75 0 1 1 1.06 1.06L11.06 8.8l.72.72a.75.75 0 1 1-1.06 1.06L10 9.86l-.72.72a.75.75 0 0 1-1.06-1.06l.72-.72-.72-.72a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900" x-text="toast.title"></p>
                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600" x-text="toast.message"></p>
                            </div>

                            <button type="button" class="text-slate-400 transition hover:text-slate-700" @click="$store.notifications.remove(toast.id)">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <aside class="shell-sidebar">
                <div class="flex h-full flex-col">
                    <div class="shell-sidebar-brand">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5">
                            <x-application-logo />
                            <p class="shell-sidebar-title text-xs truncate">{{ config('app.name', 'Digital Life Manager') }}</p>
                        </a>
                    </div>

                    @php
                        $navigationItems = [
                            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                            ['label' => 'Tasks', 'route' => 'tasks.index', 'active' => 'tasks.*'],
                            ['label' => 'Expenses', 'route' => 'expenses.index', 'active' => 'expenses.*'],
                            ['label' => 'Budgets', 'route' => 'budgets.index', 'active' => 'budgets.*'],
                            ['label' => 'Notes', 'route' => 'notes.index', 'active' => 'notes.*'],
                            ['label' => 'Mood', 'route' => 'moods.index', 'active' => 'moods.*'],
                            ['label' => 'Profile', 'route' => 'profile.edit', 'active' => 'profile.*'],
                        ];
                    @endphp

                    <div class="shell-sidebar-section">
                        <p class="shell-sidebar-label">Navigation</p>

                        <nav class="mt-3 space-y-1">
                            @foreach ($navigationItems as $item)
                                <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                                    {{ $item['label'] }}
                                </x-responsive-nav-link>
                            @endforeach
                        </nav>
                    </div>

                    <div class="shell-sidebar-footer">
                        <div class="shell-sidebar-note">
                            <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? config('app.name', 'Laravel') }}</p>
                            <p class="mt-1 text-xs text-slate-500">Profile and account actions live in the top bar.</p>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="shell-main">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/60 shadow-sm">
                    <div class="page-container py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
            </div>

            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                x-cloak
                x-show="sidebarOpen"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-72 border-r border-slate-200/60 bg-white shadow-2xl lg:hidden"
            >
                <div class="flex h-full flex-col">
                    <div class="flex items-center justify-between border-b border-slate-200/60 px-4 py-3">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo />
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Laravel') }}</p>
                                <p class="text-xs text-slate-500">Dashboard</p>
                            </div>
                        </a>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                            @click="sidebarOpen = false"
                        >
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 px-4 py-6">
                        <p class="shell-sidebar-label">Navigation</p>

                        <nav class="mt-3 space-y-1">
                            @foreach ($navigationItems as $item)
                                <x-responsive-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'])">
                                    {{ $item['label'] }}
                                </x-responsive-nav-link>
                            @endforeach
                        </nav>
                    </div>

                    <div class="border-t border-slate-200/60 p-6">
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="mt-4 flex flex-col gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn-secondary w-full">Profile</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn-danger w-full">Log Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </body>
</html>
