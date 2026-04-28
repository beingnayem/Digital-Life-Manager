<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Workspace overview</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container space-y-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-slate-500">Tasks completed today</p>
                        <div class="mt-4 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-4xl font-semibold tracking-tight text-slate-900">
                                    {{ $stats['tasks_completed_today'] }}
                                </p>
                                <p class="mt-2 text-sm text-slate-500">Completed tasks recorded for today.</p>
                            </div>
                            <div class="rounded-2xl bg-primary-50 px-3 py-2 text-primary-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 11l3 3L22 4" />
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-slate-500">Total expenses</p>
                        <div class="mt-4 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-4xl font-semibold tracking-tight text-slate-900">
                                    ${{ number_format($stats['total_expenses'], 2) }}
                                </p>
                                <p class="mt-2 text-sm text-slate-500">Confirmed expenses across your account.</p>
                            </div>
                            <div class="rounded-2xl bg-primary-50 px-3 py-2 text-primary-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 1v22" />
                                    <path d="M17 5.5a4 4 0 0 0-3.2-1.5h-3.4A3.4 3.4 0 0 0 7 7.4c0 1.8 1.5 3.3 3.3 3.3h3.4a3.3 3.3 0 1 1 0 6.6H8.2A4 4 0 0 1 5 15.8" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-slate-500">Mood summary</p>
                        <div class="mt-4 space-y-3">
                            @if ($stats['mood_summary']['latest'])
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-2xl font-semibold tracking-tight text-slate-900">
                                            {{ $stats['mood_summary']['latest']->mood_level }}/10
                                        </p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $stats['mood_summary']['latest']->mood_label ?? 'Latest mood entry' }}
                                        </p>
                                    </div>
                                    <div class="rounded-2xl bg-primary-50 px-3 py-2 text-primary-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 21s-7-4.35-7-10a7 7 0 1 1 14 0c0 5.65-7 10-7 10Z" />
                                            <path d="M9.5 10.5h.01M14.5 10.5h.01" />
                                            <path d="M9.6 14c1.2 1 3.6 1 4.8 0" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 text-sm text-slate-500">
                                    <span class="rounded-full bg-slate-100 px-3 py-1">7-day average: {{ $stats['mood_summary']['week_average'] ?? '—' }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Entries: {{ $stats['mood_summary']['entries_count'] }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Latest: {{ $stats['mood_summary']['latest']->recorded_date?->format('M j') }}</span>
                                </div>
                            @else
                                <p class="text-sm text-slate-500">
                                    No mood entries yet. Start tracking how you feel to see trends here.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Recent notes</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">Latest saved notes</h3>
                        </div>
                        <a href="{{ route('notes.index') }}" class="link text-sm font-medium">View all notes</a>
                    </div>

                    <div class="mt-6 divide-y divide-slate-200/70">
                        @forelse ($stats['recent_notes'] as $note)
                            <a href="{{ route('notes.edit', $note) }}" class="flex items-center justify-between gap-4 py-4 transition hover:bg-slate-50">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate font-semibold text-slate-900">{{ $note->title }}</p>
                                        @if ($note->is_pinned)
                                            <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700">Pinned</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $note->category ?: 'General' }} · Updated {{ $note->updated_at?->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="shrink-0 text-sm font-medium text-primary-600">Open</span>
                            </a>
                        @empty
                            <p class="py-4 text-sm text-slate-500">No notes yet. Create one to see recent activity here.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
