<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Edit Expense</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-secondary">Back to Expenses</a>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="card mx-auto max-w-3xl">
                <div class="card-body">
                    <div class="mb-6 flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm text-slate-500">Update the expense details and save your changes.</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $expense->description ?: 'Expense #' . $expense->id }}</h2>
                        </div>
                        <a href="{{ route('dashboard') }}" class="link text-sm">Dashboard</a>
                    </div>

                    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="amount">Amount</label>
                                <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $expense->amount) }}" class="form-input w-full" required />
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="date">Date</label>
                                <input id="date" name="date" type="date" value="{{ old('date', optional($expense->date)->format('Y-m-d')) }}" class="form-input w-full" required />
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="category">Category</label>
                                <input id="category" name="category" type="text" value="{{ old('category', $expense->category) }}" class="form-input w-full" required />
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="payment_method">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-input w-full">
                                    @foreach (['cash' => 'Cash', 'card' => 'Card', 'check' => 'Check', 'bank_transfer' => 'Bank Transfer', 'mobile_payment' => 'Mobile Payment', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('payment_method', $expense->payment_method) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-input w-full">
                                @foreach (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'disputed' => 'Disputed', 'refunded' => 'Refunded'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $expense->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-input w-full" rows="4" placeholder="Optional notes...">{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a href="{{ route('expenses.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">Save Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
