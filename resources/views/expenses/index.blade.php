<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Finances</p>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">Expense Tracker</h1>
            </div>
            <button x-data @click="$dispatch('open-expense-modal', { mode: 'create' })" class="btn-primary">+ New Expense</button>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <!-- Filters -->
            <div class="card mb-6">
                <div class="card-body">
                    <p class="mb-4 text-sm font-semibold text-slate-900">Filters</p>
                    <form method="GET" action="{{ route('expenses.index') }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="form-label">Category</label>
                            <select name="category" class="form-input w-full text-sm">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Status</label>
                            <select name="status" class="form-input w-full text-sm">
                                <option value="">All status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="disputed" {{ request('status') === 'disputed' ? 'selected' : '' }}>Disputed</option>
                                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Start Date</label>
                            <input name="start_date" type="date" value="{{ request('start_date') }}" class="form-input w-full text-sm" />
                        </div>

                        <div>
                            <label class="form-label">End Date</label>
                            <input name="end_date" type="date" value="{{ request('end_date') }}" class="form-input w-full text-sm" />
                        </div>

                        <div>
                            <label class="form-label">Min Amount</label>
                            <input name="min_amount" type="number" step="0.01" value="{{ request('min_amount') }}" placeholder="0.00" class="form-input w-full text-sm" />
                        </div>

                        <div>
                            <label class="form-label">Max Amount</label>
                            <input name="max_amount" type="number" step="0.01" value="{{ request('max_amount') }}" placeholder="0.00" class="form-input w-full text-sm" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Search</label>
                            <input name="search" value="{{ request('search') }}" placeholder="Search description or category..." class="form-input w-full text-sm" />
                        </div>

                        <div class="flex gap-2 md:col-span-2 lg:col-span-4">
                            <button type="submit" class="btn-secondary">Filter</button>
                            <a href="{{ route('expenses.index') }}" class="btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase text-slate-500">Total Expenses</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            ${{ number_format($expenses->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase text-slate-500">Confirmed</p>
                        <p class="mt-2 text-2xl font-bold text-green-600">
                            ${{ number_format($expenses->where('status', 'confirmed')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase text-slate-500">Pending</p>
                        <p class="mt-2 text-2xl font-bold text-yellow-600">
                            ${{ number_format($expenses->where('status', 'pending')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="mb-6 grid gap-6 lg:grid-cols-2">
                <!-- 30-Day Trend -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-4 text-sm font-semibold text-slate-900">30-Day Trend</h3>
                        <div class="flex h-64 items-center justify-center">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-4 text-sm font-semibold text-slate-900">By Category</h3>
                        <div class="flex h-64 items-center justify-center">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="mb-6 grid gap-6 lg:grid-cols-2">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-4 text-sm font-semibold text-slate-900">By Status</h3>
                        <div class="flex h-64 items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Recent Expenses</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($expenses as $expense)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $expense->date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-900">{{ $expense->description ?? '—' }}</p>
                                            <p class="text-xs text-slate-500">{{ $expense->payment_method ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ ucfirst($expense->category) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $expense->status === 'confirmed' ? 'bg-green-50 text-green-700' : ($expense->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : ($expense->status === 'disputed' ? 'bg-red-50 text-red-700' : 'bg-slate-50 text-slate-700')) }}">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium">
                                        <button x-data @click="$dispatch('open-expense-modal', { mode: 'edit', expense: {{ json_encode($expense) }} })" class="text-primary-600 hover:text-primary-700 mr-3">Edit</button>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Delete this expense?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        No expenses found. <a href="{{ route('expenses.index') }}" class="text-primary-600 hover:underline">Create one</a>.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $expenses->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense Modal (create/edit) --}}
    <div x-data x-cloak @open-expense-modal.window="(e) => { $store.expenseModal.open = true; $store.expenseModal.mode = e.detail.mode; if(e.detail.mode === 'edit') { $store.expenseModal.expense = e.detail.expense; } else { $store.expenseModal.expense = { amount: '', category: '', description: '', date: new Date().toISOString().split('T')[0], payment_method: 'card', status: 'confirmed' }; } }">
        <div x-show="$store.expenseModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40" @click="$store.expenseModal.open=false"></div>
            <div class="relative w-full max-w-2xl">
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="$store.expenseModal.mode === 'create' ? 'Add Expense' : 'Edit Expense'"></h3>
                            <button @click="$store.expenseModal.open=false" class="text-slate-500">✕</button>
                        </div>

                        <form :action="$store.expenseModal.mode === 'create' ? '{{ route('expenses.store') }}' : '/expenses/' + ($store.expenseModal.expense.id ?? '')" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <template x-if="$store.expenseModal.mode === 'edit'">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="form-label">Amount</label>
                                    <input name="amount" type="number" step="0.01" x-model="$store.expenseModal.expense.amount" class="form-input w-full" placeholder="0.00" required />
                                </div>

                                <div>
                                    <label class="form-label">Date</label>
                                    <input name="date" type="date" x-model="$store.expenseModal.expense.date" class="form-input w-full" required />
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="form-label">Category</label>
                                    <input name="category" x-model="$store.expenseModal.expense.category" class="form-input w-full" placeholder="e.g., Food, Transport" required />
                                </div>

                                <div>
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" x-model="$store.expenseModal.expense.payment_method" class="form-input w-full">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="check">Check</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_payment">Mobile Payment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Status</label>
                                <select name="status" x-model="$store.expenseModal.expense.status" class="form-input w-full">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="disputed">Disputed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Description</label>
                                <textarea name="description" x-model="$store.expenseModal.expense.description" class="form-input w-full" rows="3" placeholder="Optional notes..."></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="$store.expenseModal.open=false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary" x-text="$store.expenseModal.mode === 'create' ? 'Create' : 'Save'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('expenseModal', {
                open: false,
                mode: 'create',
                expense: { }
            });
        });

        // Initialize charts when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($chartData);

            // 30-Day Trend Chart
            if (chartData.trend_30day && chartData.trend_30day.length > 0) {
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.trend_30day.map(d => d.label),
                        datasets: [{
                            label: 'Daily Expenses',
                            data: chartData.trend_30day.map(d => d.value),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: v => '$' + v.toFixed(0) }
                            }
                        }
                    }
                });
            }

            // Category Breakdown Chart
            if (chartData.category_breakdown && chartData.category_breakdown.length > 0) {
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                const colors = ['#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.category_breakdown.map(d => d.label),
                        datasets: [{
                            data: chartData.category_breakdown.map(d => d.value),
                            backgroundColor: colors.slice(0, chartData.category_breakdown.length),
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 12, font: { size: 12 } }
                            }
                        }
                    }
                });
            }

            // Status Breakdown Chart (Bar)
            if (chartData.status_breakdown && chartData.status_breakdown.length > 0) {
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.status_breakdown.map(d => d.label),
                        datasets: [{
                            label: 'Amount',
                            data: chartData.status_breakdown.map(d => d.value),
                            backgroundColor: chartData.status_breakdown.map(d => d.color),
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { callback: v => '$' + v.toFixed(0) }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
