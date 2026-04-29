<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Create New</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Add Budget</h2>
            </div>
            <a href="{{ route('budgets.index') }}" class="flex items-center gap-2 rounded-lg px-6 py-2.5 font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="mx-auto max-w-3xl">
                <!-- Main Form Card -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 px-8 py-8">
                        <h2 class="text-2xl font-bold text-slate-900">Set Your Budget</h2>
                        <p class="mt-2 text-slate-600">Define spending limits by category and stay on top of your finances.</p>
                    </div>

                    <!-- Form Content -->
                    <div class="p-8">
                        <form method="POST" action="{{ route('budgets.store') }}" class="space-y-8">
                            @csrf

                            <!-- Category and Month Row -->
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">Category Name</label>
                                    <div class="relative">
                                        <input
                                            id="category"
                                            name="category"
                                            type="text"
                                            value="{{ old('category') }}"
                                            placeholder="e.g., Food, Entertainment, Shopping"
                                            class="form-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 transition-colors focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                                            required
                                        />
                                    </div>
                                    @error('category')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18.707 5.293a1 1 0 00-1.414 0L10 12.586 2.707 5.293a1 1 0 00-1.414 1.414l7.293 7.293a1 1 0 001.414 0l7.293-7.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">Month</label>
                                    <div class="relative">
                                        <input
                                            id="month_year"
                                            name="month_year"
                                            type="month"
                                            value="{{ old('month_year', now()->format('Y-m')) }}"
                                            class="form-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-slate-900 transition-colors focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                                            required
                                        />
                                    </div>
                                    @error('month_year')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18.707 5.293a1 1 0 00-1.414 0L10 12.586 2.707 5.293a1 1 0 00-1.414 1.414l7.293 7.293a1 1 0 001.414 0l7.293-7.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Limit and Threshold Row -->
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">Monthly Spending Limit</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 text-slate-500 font-semibold">$</span>
                                        <input
                                            id="limit_amount"
                                            name="limit_amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value="{{ old('limit_amount') }}"
                                            placeholder="0.00"
                                            class="form-input w-full rounded-lg border border-slate-200 bg-white pl-8 pr-4 py-3 text-slate-900 placeholder-slate-400 transition-colors focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                                            required
                                        />
                                    </div>
                                    @error('limit_amount')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18.707 5.293a1 1 0 00-1.414 0L10 12.586 2.707 5.293a1 1 0 00-1.414 1.414l7.293 7.293a1 1 0 001.414 0l7.293-7.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">Alert Threshold</label>
                                    <div class="relative">
                                        <input
                                            id="alert_threshold"
                                            name="alert_threshold"
                                            type="number"
                                            min="1"
                                            max="100"
                                            value="{{ old('alert_threshold', 80) }}"
                                            class="form-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-slate-900 transition-colors focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                                        />
                                        <span class="absolute right-4 top-3.5 text-slate-500 font-semibold">%</span>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-600">Alert triggers when spending reaches this percentage (default: 80%)</p>
                                    @error('alert_threshold')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18.707 5.293a1 1 0 00-1.414 0L10 12.586 2.707 5.293a1 1 0 00-1.414 1.414l7.293 7.293a1 1 0 001.414 0l7.293-7.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Info Cards -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <!-- How It Works Card -->
                                <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4">
                                    <div class="flex gap-3">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-200">
                                            <svg class="h-5 w-5 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-blue-900">How it works</h3>
                                            <p class="mt-1 text-xs text-blue-800 leading-relaxed">When confirmed expenses reach your limit, you'll get an alert email. One per month per category.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tip Card -->
                                <div class="rounded-xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 p-4">
                                    <div class="flex gap-3">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-200">
                                            <svg class="h-5 w-5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-emerald-900">Pro tip</h3>
                                            <p class="mt-1 text-xs text-emerald-800 leading-relaxed">Only confirmed expenses count. Pending expenses won't trigger alerts yet.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Form Footer with Actions -->
                    <div class="flex gap-3 border-t border-slate-200 bg-slate-50 px-8 py-6">
                        <a href="{{ route('budgets.index') }}" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button
                            type="submit"
                            form="budgetForm"
                            class="flex-1 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-center font-semibold text-white shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all"
                            onclick="document.querySelector('form[action=\'{{ route('budgets.store') }}\']').submit()"
                        >
                            Create Budget
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
