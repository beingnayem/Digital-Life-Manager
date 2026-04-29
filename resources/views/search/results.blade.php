@extends('layouts.app')

@section('content')
    <div class="page-container">
        <h1 class="text-lg font-semibold">Search results for "{{ $q }}"</h1>

        <div class="grid gap-6 mt-4 lg:grid-cols-2">
            <div class="card">
                <h2 class="font-semibold">Tasks</h2>
                @if($tasks->isEmpty())
                    <p class="text-sm text-slate-500">No matching tasks.</p>
                @else
                    <ul class="mt-2 space-y-2">
                        @foreach($tasks as $task)
                            <li>
                                <a href="{{ route('tasks.edit', $task) }}" class="text-primary-600 hover:underline">{{ $task->title }}</a>
                                <div class="text-xs text-slate-500">Due: {{ optional($task->due_date)->format('Y-m-d') }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">{{ $tasks->appends(request()->query())->links() }}</div>
                @endif
            </div>

            <div class="card">
                <h2 class="font-semibold">Notes</h2>
                @if($notes->isEmpty())
                    <p class="text-sm text-slate-500">No matching notes.</p>
                @else
                    <ul class="mt-2 space-y-2">
                        @foreach($notes as $note)
                            <li>
                                <a href="{{ route('notes.edit', $note) }}" class="text-primary-600 hover:underline">{{ $note->title }}</a>
                                <div class="text-xs text-slate-500">{{ Str::limit(strip_tags($note->content), 80) }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">{{ $notes->appends(request()->query())->links() }}</div>
                @endif
            </div>

            <div class="card">
                <h2 class="font-semibold">Expenses</h2>
                @if($expenses->isEmpty())
                    <p class="text-sm text-slate-500">No matching expenses.</p>
                @else
                    <ul class="mt-2 space-y-2">
                        @foreach($expenses as $expense)
                            <li>
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-primary-600 hover:underline">{{ $expense->description ?? 'Expense #' . $expense->id }}</a>
                                <div class="text-xs text-slate-500">{{ $expense->category }} • {{ money($expense->amount) }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">{{ $expenses->appends(request()->query())->links() }}</div>
                @endif
            </div>

            <div class="card">
                <h2 class="font-semibold">Budgets</h2>
                @if($budgets->isEmpty())
                    <p class="text-sm text-slate-500">No matching budgets.</p>
                @else
                    <ul class="mt-2 space-y-2">
                        @foreach($budgets as $budget)
                            <li>
                                <a href="{{ route('budgets.edit', $budget) }}" class="text-primary-600 hover:underline">{{ $budget->name }}</a>
                                <div class="text-xs text-slate-500">{{ money($budget->limit_amount) }} • {{ $budget->frequency ?? 'monthly' }}</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">{{ $budgets->appends(request()->query())->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
