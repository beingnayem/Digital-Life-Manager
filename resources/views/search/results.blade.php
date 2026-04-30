@extends('layouts.app')

@section('content')
    <div class="page-container">
        <h1 class="text-lg font-semibold">Search results for "{{ $q }}"</h1>

        <div class="search-results-grid grid gap-6 mt-4 lg:grid-cols-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Tasks</h2>
                    @if($tasks->isEmpty())
                        <p class="card-empty">No matching tasks.</p>
                    @else
                        <ul class="card-list mt-2">
                            @foreach($tasks as $task)
                                <li class="card-item">
                                    <a href="{{ route('tasks.edit', $task) }}" class="card-link">{{ $task->title }}</a>
                                    <div class="card-meta">Due: {{ optional($task->due_date)->format('Y-m-d') }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer mt-4">{{ $tasks->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Notes</h2>
                    @if($notes->isEmpty())
                        <p class="card-empty">No matching notes.</p>
                    @else
                        <ul class="card-list mt-2">
                            @foreach($notes as $note)
                                <li class="card-item">
                                    <a href="{{ route('notes.edit', $note) }}" class="card-link">{{ $note->title }}</a>
                                    <div class="card-desc">{{ Str::limit(strip_tags($note->content), 160) }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer mt-4">{{ $notes->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Expenses</h2>
                    @if($expenses->isEmpty())
                        <p class="card-empty">No matching expenses.</p>
                    @else
                        <ul class="card-list mt-2">
                            @foreach($expenses as $expense)
                                <li class="card-item">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="card-link">{{ $expense->description ?? 'Expense #' . $expense->id }}</a>
                                    <div class="card-meta">{{ $expense->category }} • ${{ number_format((float) $expense->amount, 2) }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer mt-4">{{ $expenses->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Budgets</h2>
                    @if($budgets->isEmpty())
                        <p class="card-empty">No matching budgets.</p>
                    @else
                        <ul class="card-list mt-2">
                            @foreach($budgets as $budget)
                                <li class="card-item">
                                    <a href="{{ route('budgets.edit', $budget) }}" class="card-link">{{ $budget->name }}</a>
                                    <div class="card-meta">${{ number_format((float) $budget->limit_amount, 2) }} • {{ $budget->frequency ?? 'monthly' }}</div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer mt-4">{{ $budgets->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
