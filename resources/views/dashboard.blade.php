<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Workspace overview</p>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container space-y-8">
            {{-- Summary Cards with Trends --}}
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- Tasks Card --}}
                <div class="card overflow-hidden">
                    <div class="card-body relative">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500">Tasks completed</p>
                                <p class="mt-2 text-4xl font-bold tracking-tight text-slate-900">
                                    {{ $stats['tasks_completed_today'] }}
                                </p>
                                <p class="mt-3 text-xs text-slate-500">Completed today</p>
                            </div>
                            <div class="rounded-2xl bg-blue-50 p-3 text-blue-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @if ($stats['tasks_trend'] > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 5.586L6.707.293a1 1 0 00-1.414 1.414L10.586 7 5.293 12.293a1 1 0 101.414 1.414L12 8.414l5.293 5.293a1 1 0 001.414-1.414L13.414 7l5.293-5.293a1 1 0 00-1.414-1.414L12 5.586z" transform="rotate(180)" />
                                    </svg>
                                    +{{ $stats['tasks_trend'] }}%
                                </span>
                            @elseif ($stats['tasks_trend'] < 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 5.586L6.707.293a1 1 0 00-1.414 1.414L10.586 7 5.293 12.293a1 1 0 101.414 1.414L12 8.414l5.293 5.293a1 1 0 001.414-1.414L13.414 7l5.293-5.293a1 1 0 00-1.414-1.414L12 5.586z" />
                                    </svg>
                                    {{ $stats['tasks_trend'] }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 10a7 7 0 1114 0 7 7 0 01-14 0z" />
                                    </svg>
                                    No change
                                </span>
                            @endif
                            <span class="text-xs text-slate-500">vs yesterday</span>
                        </div>
                    </div>
                </div>

                {{-- Expenses Card --}}
                <div class="card overflow-hidden">
                    <div class="card-body relative">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500">Expenses this week</p>
                                <p class="mt-2 text-4xl font-bold tracking-tight text-slate-900">
                                    ${{ number_format($stats['total_expenses'], 0) }}
                                </p>
                                <p class="mt-3 text-xs text-slate-500">Confirmed transactions</p>
                            </div>
                            <div class="rounded-2xl bg-green-50 p-3 text-green-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @if ($stats['expenses_trend'] > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 5.586L6.707.293a1 1 0 00-1.414 1.414L10.586 7 5.293 12.293a1 1 0 101.414 1.414L12 8.414l5.293 5.293a1 1 0 001.414-1.414L13.414 7l5.293-5.293a1 1 0 00-1.414-1.414L12 5.586z" />
                                    </svg>
                                    +{{ $stats['expenses_trend'] }}%
                                </span>
                            @elseif ($stats['expenses_trend'] < 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12 5.586L6.707.293a1 1 0 00-1.414 1.414L10.586 7 5.293 12.293a1 1 0 101.414 1.414L12 8.414l5.293 5.293a1 1 0 001.414-1.414L13.414 7l5.293-5.293a1 1 0 00-1.414-1.414L12 5.586z" transform="rotate(180)" />
                                    </svg>
                                    {{ $stats['expenses_trend'] }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 10a7 7 0 1114 0 7 7 0 01-14 0z" />
                                    </svg>
                                    No change
                                </span>
                            @endif
                            <span class="text-xs text-slate-500">vs last week</span>
                        </div>
                    </div>
                </div>

                {{-- Mood Card --}}
                <div class="card overflow-hidden">
                    <div class="card-body relative">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500">Current mood</p>
                                @if ($stats['mood_summary']['latest'])
                                    <p class="mt-2 text-4xl font-bold tracking-tight text-slate-900">
                                        {{ $stats['mood_summary']['latest']->mood_level }}<span class="text-xl text-slate-400">/10</span>
                                    </p>
                                    <p class="mt-3 text-xs text-slate-500">
                                        {{ $stats['mood_summary']['latest']->mood_label ?? 'Recorded' }} today
                                    </p>
                                @else
                                    <p class="mt-2 text-2xl font-semibold text-slate-500">No entry</p>
                                    <p class="mt-3 text-xs text-slate-500">Tap to add today's mood</p>
                                @endif
                            </div>
                            <div class="rounded-2xl bg-orange-50 p-3 text-orange-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                7-day avg: {{ $stats['mood_summary']['week_average'] ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Notes Card --}}
                <div class="card overflow-hidden">
                    <div class="card-body relative">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-500">Recent notes</p>
                                <p class="mt-2 text-4xl font-bold tracking-tight text-slate-900">
                                    {{ $stats['recent_notes']->count() }}
                                </p>
                                <p class="mt-3 text-xs text-slate-500">Active notes saved</p>
                            </div>
                            <div class="rounded-2xl bg-purple-50 p-3 text-purple-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('notes.index') }}" class="inline-flex items-center text-xs font-semibold text-primary-600 hover:text-primary-700">
                                View all →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid gap-4 lg:grid-cols-2">
                {{-- Tasks Chart --}}
                <div class="card">
                    <div class="card-body">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-900">Task Completion Trend</h3>
                            <p class="mt-1 text-sm text-slate-500">Last 7 days</p>
                        </div>
                        <div class="relative h-64">
                            <canvas id="taskChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Expense Chart --}}
                <div class="card">
                    <div class="card-body">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-900">Daily Expenses</h3>
                            <p class="mt-1 text-sm text-slate-500">Last 7 days</p>
                        </div>
                        <div class="relative h-64">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Mood Chart --}}
                <div class="card">
                    <div class="card-body">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-900">Mood Trend</h3>
                            <p class="mt-1 text-sm text-slate-500">Last 7 days (1-10 scale)</p>
                        </div>
                        <div class="relative h-64">
                            <canvas id="moodChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Category Chart --}}
                @if ($stats['charts']['expenses_by_category']->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-slate-900">Expenses by Category</h3>
                                <p class="mt-1 text-sm text-slate-500">Current month</p>
                            </div>
                            <div class="relative h-64">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Activity Sections --}}
            <div class="grid gap-4 lg:grid-cols-3">
                {{-- Recent Tasks --}}
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200/70">
                            <h3 class="font-semibold text-slate-900">Recent Tasks</h3>
                            <a href="{{ route('tasks.index') }}" class="link text-xs">View all</a>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse ($stats['recent_tasks']->take(3) as $task)
                                <a href="{{ route('tasks.edit', $task) }}" class="group block truncate rounded-lg p-3 transition hover:bg-slate-50">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 shrink-0">
                                            @if ($task->status === 'completed')
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-green-100">
                                                    <svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif ($task->priority === 'high')
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-100">
                                                    <svg class="h-2 w-2 fill-red-600" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border-2 border-slate-300"></span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-900 group-hover:text-primary-600">
                                                {{ $task->title }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $task->due_date?->format('M d') ?? 'No date' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="py-4 text-center text-sm text-slate-500">No recent tasks</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Recent Expenses --}}
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200/70">
                            <h3 class="font-semibold text-slate-900">Recent Expenses</h3>
                            <a href="{{ route('expenses.index') }}" class="link text-xs">View all</a>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse ($stats['recent_expenses']->take(3) as $expense)
                                <a href="{{ route('expenses.edit', $expense) }}" class="group block truncate rounded-lg p-3 transition hover:bg-slate-50">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-900 group-hover:text-primary-600">
                                                {{ $expense->description }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $expense->category }} · {{ $expense->date?->format('M d') }}
                                            </p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="text-sm font-semibold text-slate-900">
                                                ${{ number_format($expense->amount, 2) }}
                                            </p>
                                            <p class="text-xs">
                                                @if ($expense->status === 'confirmed')
                                                    <span class="text-green-600">✓</span>
                                                @else
                                                    <span class="text-yellow-600">●</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="py-4 text-center text-sm text-slate-500">No recent expenses</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Recent Notes --}}
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-200/70">
                            <h3 class="font-semibold text-slate-900">Recent Notes</h3>
                            <a href="{{ route('notes.index') }}" class="link text-xs">View all</a>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse ($stats['recent_notes']->take(3) as $note)
                                <a href="{{ route('notes.edit', $note) }}" class="group block rounded-lg bg-slate-50/50 p-3 transition hover:bg-slate-100">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-slate-900 group-hover:text-primary-600">
                                                {{ $note->title }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $note->category ? ucfirst($note->category) : 'General' }} · {{ $note->updated_at?->diffForHumans() ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="shrink-0 rounded-full bg-primary-100 px-3 py-1 text-center text-xs font-semibold text-primary-700">
                                            {{ $note->is_pinned ? 'Pinned' : 'Note' }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="py-4 text-center text-sm text-slate-500">No recent notes yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stats = @json($stats);
            if (window.initAllCharts) {
                window.initAllCharts(stats);
            }
        });
    </script>
</x-app-layout>
