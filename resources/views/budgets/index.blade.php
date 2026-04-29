<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Financial Control</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Budget Management</h2>
                <p class="mt-2 text-sm text-slate-600">Track and manage your spending across different categories</p>
            </div>
            <a href="{{ route('budgets.create') }}" class="flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 font-semibold text-white shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Budget
            </a>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-emerald-100 p-4 text-sm text-emerald-800 shadow-sm">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <div>{{ session('status') }}</div>
                    </div>
                </div>
            @endif

            @if ($budgets->count() > 0)
                <!-- Summary Stats -->
                @php
                    $totalLimit = $budgets->sum('limit_amount');
                    $totalSpent = $budgets->sum('spent_amount');
                    $totalRemaining = $totalLimit - $totalSpent;
                    $overallPercentage = $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0;
                @endphp

                <div class="mb-8 grid gap-4 sm:grid-cols-3">
                    <!-- Total Budget Card -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-slate-600">Total Budget</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-slate-900">${{ number_format($totalLimit, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Across {{ $budgets->count() }} budget{{ $budgets->count() !== 1 ? 's' : '' }}</p>
                    </div>

                    <!-- Total Spent Card -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-slate-600">Total Spent</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100">
                                <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-slate-900">${{ number_format($totalSpent, 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ number_format($overallPercentage, 0) }}% of total</p>
                    </div>

                    <!-- Remaining Card -->
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-slate-600">Remaining</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-slate-900">${{ number_format(max($totalRemaining, 0), 2) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Available to spend</p>
                    </div>
                </div>

                <!-- Budget Cards Grid -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($budgets as $budget)
                        @php
                            $percentage = $budget->getUtilizationPercentage();
                            $status = $percentage >= 100 ? 'exceeded' : ($percentage >= 80 ? 'warning' : 'on-track');
                            $statusColor = [
                                'exceeded' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'badge' => 'bg-red-100 text-red-800', 'icon' => 'text-red-600', 'bar' => 'bg-red-500'],
                                'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-100 text-yellow-800', 'icon' => 'text-yellow-600', 'bar' => 'bg-yellow-500'],
                                'on-track' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-800', 'icon' => 'text-emerald-600', 'bar' => 'bg-emerald-500'],
                            ];
                            $colors = $statusColor[$status];
                        @endphp

                        <div class="group rounded-xl border {{ $colors['border'] }} {{ $colors['bg'] }} p-6 shadow-sm hover:shadow-md transition-all duration-200 hover:border-slate-300">
                            <!-- Header -->
                            <div class="mb-4 flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $budget->category }}</h3>
                                    <p class="mt-1 text-xs text-slate-600">
                                        {{ \Carbon\CarbonImmutable::createFromFormat('Y-m', $budget->month_year)->format('F Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full {{ $colors['badge'] }} px-2.5 py-1 text-xs font-semibold">
                                    @if ($status === 'exceeded')
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 0 1 5.11 6.524a6 6 0 0 1 8.367 8.366L13.477 14.89ZM9.5 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                                        </svg>
                                        Exceeded
                                    @elseif ($status === 'warning')
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.125 2.357-1.125 3.03 0l6.28 10.55c.668 1.12-.262 2.53-1.515 2.53H3.72c-1.253 0-2.183-1.41-1.515-2.53L8.485 2.495ZM10 5a.75.75 0 0 0-.75.75v3.5a.75.75 0 0 0 1.5 0v-3.5A.75.75 0 0 0 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                        </svg>
                                        Warning
                                    @else
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                        On Track
                                    @endif
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="mb-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">${{ number_format($budget->spent_amount, 2) }}</p>
                                        <p class="text-xs text-slate-600">of ${{ number_format($budget->limit_amount, 2) }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-slate-900">{{ number_format($percentage, 0) }}%</p>
                                </div>
                                <div class="relative h-3 w-full overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        class="{{ $colors['bar'] }} h-full rounded-full transition-all duration-500"
                                        style="width: {{ min($percentage, 100) }}%"
                                    ></div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="mb-4 flex items-center justify-between text-sm">
                                <div>
                                    <p class="text-xs text-slate-600">Remaining</p>
                                    <p class="font-semibold text-slate-900">${{ number_format(max($budget->limit_amount - $budget->spent_amount, 0), 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-600">Daily Avg</p>
                                    <p class="font-semibold text-slate-900">${{ number_format($budget->spent_amount / max(now()->diffInDays(\Carbon\CarbonImmutable::createFromFormat('Y-m', $budget->month_year)->startOfMonth()), 1), 2) }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-4 border-t border-slate-200 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('budgets.edit', $budget) }}" class="flex-1 rounded-lg bg-white px-3 py-2 text-center text-sm font-semibold text-primary-600 hover:bg-primary-50 transition-colors border border-primary-200">
                                    Edit
                                </a>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this budget?')" class="w-full rounded-lg bg-white px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors border border-red-200">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($budgets->hasPages())
                    <div class="mt-8">
                        {{ $budgets->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-12 text-center">
                    <div class="mx-auto w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">No budgets yet</h3>
                    <p class="text-slate-600 mb-6 max-w-sm mx-auto">Start managing your spending by creating your first budget. Set monthly limits and track expenses by category.</p>
                    <a href="{{ route('budgets.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 font-semibold text-white shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create First Budget
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
