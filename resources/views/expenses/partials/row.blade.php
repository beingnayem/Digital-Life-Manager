<tr data-expense-id="{{ $expense->id }}" data-expense-amount="{{ $expense->amount }}" data-expense-status="{{ $expense->status }}" class="transition-colors duration-200 hover:bg-slate-50">
    <td class="px-4 py-3 text-sm text-slate-600">{{ $expense->date->format('M d, Y') }}</td>
    <td class="px-4 py-3">
        <div class="min-w-0">
            <p class="truncate font-medium text-slate-900">{{ $expense->description ?? '—' }}</p>
            <p class="text-xs text-slate-500">{{ $expense->payment_method ?? 'N/A' }}</p>
        </div>
    </td>
    <td class="px-4 py-3 text-sm">
        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
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
        <button x-data @click="$dispatch('open-expense-modal', { mode: 'edit', expense: {{ json_encode($expense) }} })" class="mr-3 text-primary-600 transition-colors duration-200 hover:text-primary-700">Edit</button>
        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline" data-ajax-row-form="expense" data-record-id="{{ $expense->id }}" data-record-action="delete">
            @csrf
            @method('DELETE')
            <button type="submit" data-loading-label="Deleting..." class="text-red-600 transition-colors duration-200 hover:text-red-700">Delete</button>
        </form>
    </td>
</tr>
