<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $expenses = $request->user()
            ->expenses()
            ->latest('date')
            ->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cash,card,check,bank_transfer,mobile_payment,other'],
            'date' => ['required', 'date'],
            'receipt_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:pending,confirmed,disputed,refunded'],
            'tags' => ['nullable', 'array'],
            'budget_alert_sent' => ['sometimes', 'boolean'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Expense::create($validated);

        return Redirect::route('expenses.index')->with('status', 'expense-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): View
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cash,card,check,bank_transfer,mobile_payment,other'],
            'date' => ['required', 'date'],
            'receipt_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:pending,confirmed,disputed,refunded'],
            'tags' => ['nullable', 'array'],
            'budget_alert_sent' => ['sometimes', 'boolean'],
        ]);

        $expense->update($validated);

        return Redirect::route('expenses.index')->with('status', 'expense-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return Redirect::route('expenses.index')->with('status', 'expense-deleted');
    }
}