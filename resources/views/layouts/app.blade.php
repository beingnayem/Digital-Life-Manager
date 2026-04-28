<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="shell-app">
            <aside class="shell-sidebar">
                <div class="flex h-full flex-col">
                    <div class="shell-sidebar-brand">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <x-application-logo class="h-10 w-10 rounded-2xl bg-primary-50 p-2 text-primary-600" />
                            <div>
                                <p class="shell-sidebar-title">{{ config('app.name', 'Laravel') }}</p>
                                <p class="shell-sidebar-subtitle">Workspace dashboard</p>
                            </div>
                        </a>
                    </div>

                    @php
                        $navigationItems = [
                            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                            ['label' => 'Tasks', 'route' => 'tasks.index', 'active' => 'tasks.*'],
                            ['label' => 'Expenses', 'route' => 'expenses.index', 'active' => 'expenses.*'],
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
                {{ $slot }}
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
                    <div class="flex items-center justify-between border-b border-slate-200/60 px-6 py-5">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                            <x-application-logo class="h-10 w-10 rounded-2xl bg-primary-50 p-2 text-primary-600" />
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Laravel') }}</p>
                                <p class="text-xs text-slate-500">Workspace dashboard</p>
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
