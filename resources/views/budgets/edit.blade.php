<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Edit</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $budget->category }} Budget</h2>
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
                        <h2 class="text-2xl font-bold text-slate-900">Update Budget</h2>
                        <p class="mt-2 text-slate-600">Modify your spending limits and alert settings for this category.</p>
                    </div>

                    <!-- Form Content -->
                    <div class="p-8">
                        <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-8">
                            @csrf
                            @method('PATCH')

                            <!-- Category and Month Row -->
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">Category Name</label>
                                    <div class="relative">
                                        <input
                                            id="category"
                                            name="category"
                                            type="text"
                                            value="{{ old('category', $budget->category) }}"
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
                                        <input type="text" class="form-input w-full rounded-lg border border-slate-300 bg-slate-100 px-4 py-3 text-slate-600 cursor-not-allowed font-medium" value="{{ \Carbon\CarbonImmutable::createFromFormat('Y-m', $budget->month_year)->format('F Y') }}" disabled />
                                        <input type="hidden" name="month_year" value="{{ $budget->month_year }}" />
                                    </div>
                                    <p class="mt-2 text-xs text-slate-600">This cannot be changed after creation</p>
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
                                            value="{{ old('limit_amount', $budget->limit_amount) }}"
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
                                            value="{{ old('alert_threshold', $budget->alert_threshold) }}"
                                            class="form-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-slate-900 transition-colors focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                                        />
                                        <span class="absolute right-4 top-3.5 text-slate-500 font-semibold">%</span>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-600">Alert triggers when spending reaches this percentage</p>
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

                            <!-- Current Status Section -->
                            <div class="border-t border-b border-slate-200 py-6">
                                <h3 class="text-sm font-semibold text-slate-900 mb-4">Current Status</h3>
                                <div class="grid gap-4 md:grid-cols-4">
                                    @php
                                        $percentage = $budget->getUtilizationPercentage();
                                        $remaining = $budget->limit_amount - $budget->spent_amount;
                                    @endphp

                                    <div class="rounded-lg bg-slate-50 p-4">
                                        <p class="text-xs text-slate-600 uppercase tracking-wider font-medium">Limit</p>
                                        <p class="mt-2 text-xl font-bold text-slate-900">${{ number_format($budget->limit_amount, 2) }}</p>
                                    </div>

                                    <div class="rounded-lg bg-orange-50 p-4 border border-orange-200">
                                        <p class="text-xs text-orange-700 uppercase tracking-wider font-medium">Spent</p>
                                        <p class="mt-2 text-xl font-bold text-orange-700">${{ number_format($budget->spent_amount, 2) }}</p>
                                    </div>

                                    <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-200">
                                        <p class="text-xs text-emerald-700 uppercase tracking-wider font-medium">Remaining</p>
                                        <p class="mt-2 text-xl font-bold text-emerald-700">${{ number_format(max($remaining, 0), 2) }}</p>
                                    </div>

                                    <div class="rounded-lg bg-slate-50 p-4">
                                        <p class="text-xs text-slate-600 uppercase tracking-wider font-medium">Usage</p>
                                        <p class="mt-2 text-xl font-bold text-slate-900">{{ number_format($percentage, 0) }}%</p>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-4">
                                    <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        @php
                                            $barColor = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-yellow-500' : 'bg-emerald-500');
                                        @endphp
                                        <div
                                            class="{{ $barColor }} h-full rounded-full transition-all duration-500"
                                            style="width: {{ min($percentage, 100) }}%"
                                        ></div>
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
                            class="flex-1 rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2.5 text-center font-semibold text-white shadow-lg hover:shadow-xl hover:from-primary-700 hover:to-primary-800 transition-all"
                            onclick="document.querySelector('form[action=\'{{ route('budgets.update', $budget) }}\']').submit()"
                        >
                            Update Budget
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
