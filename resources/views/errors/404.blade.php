<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Not Found | {{ config('app.name', 'Digital Life Manager') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-white antialiased">
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.18),_transparent_32%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.14),_transparent_28%),linear-gradient(180deg,_#020617_0%,_#0f172a_52%,_#111827_100%)]"></div>
        <div class="absolute left-[-8rem] top-[-8rem] h-[24rem] w-[24rem] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute right-[-10rem] bottom-[-10rem] h-[28rem] w-[28rem] rounded-full bg-blue-500/15 blur-3xl"></div>
        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-10 sm:px-10 lg:px-12">
            <div class="grid w-full gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200 backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        Digital Life Manager
                    </div>

                    <div class="max-w-2xl space-y-5">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-cyan-200/80">404 Error</p>
                        <h1 class="text-5xl font-semibold tracking-tight text-white sm:text-6xl lg:text-7xl">
                            This page drifted off course.
                        </h1>
                        <p class="max-w-xl text-base leading-8 text-slate-300 sm:text-lg">
                            The page you were looking for no longer exists, may have moved, or is temporarily unavailable.
                            Use the links below to get back into your workspace quickly.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('dashboard') }}" class="btn-primary shadow-[0_18px_40px_rgba(37,99,235,0.35)]">
                            Go to Dashboard
                        </a>
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn-secondary border-white/15 bg-white/10 text-white hover:bg-white/15 hover:text-white">
                            Go Back
                        </a>
                        <a href="{{ url('/') }}" class="btn-secondary border-white/15 bg-white/10 text-white hover:bg-white/15 hover:text-white">
                            Home
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <a href="{{ route('tasks.index') }}" class="group rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur transition hover:-translate-y-0.5 hover:border-cyan-300/30 hover:bg-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200/80">Tasks</p>
                            <p class="mt-2 text-sm text-slate-200 group-hover:text-white">Manage your task list</p>
                        </a>
                        <a href="{{ route('notes.index') }}" class="group rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur transition hover:-translate-y-0.5 hover:border-cyan-300/30 hover:bg-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200/80">Notes</p>
                            <p class="mt-2 text-sm text-slate-200 group-hover:text-white">Open saved notes</p>
                        </a>
                        <a href="{{ route('expenses.index') }}" class="group rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur transition hover:-translate-y-0.5 hover:border-cyan-300/30 hover:bg-white/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200/80">Expenses</p>
                            <p class="mt-2 text-sm text-slate-200 group-hover:text-white">Review your spending</p>
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 -rotate-6 rounded-[2rem] border border-white/10 bg-white/5 shadow-2xl backdrop-blur-2xl"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-8 shadow-[0_30px_80px_rgba(0,0,0,0.45)] backdrop-blur-2xl sm:p-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Status</p>
                                <p class="mt-2 text-2xl font-semibold text-white">Resource unavailable</p>
                            </div>
                            <div class="rounded-2xl bg-cyan-400/10 p-3 text-cyan-300 ring-1 ring-cyan-300/20">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">What happened</p>
                                <p class="mt-2 text-sm leading-6 text-slate-300">
                                    Laravel could not resolve the requested route or file, so this premium error screen is being shown instead.
                                </p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Suggested next step</p>
                                <p class="mt-2 text-sm leading-6 text-slate-300">
                                    Return to the dashboard or use the workspace links to continue from a known location.
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between gap-4 border-t border-white/10 pt-5 text-xs text-slate-400">
                            <span>HTTP 404</span>
                            <span>{{ now()->format('M d, Y · g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
