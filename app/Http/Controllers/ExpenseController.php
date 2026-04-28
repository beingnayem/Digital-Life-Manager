<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request): View
    {
        $query = $request->user()->expenses();

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by amount range
        if ($request->has('min_amount') && $request->min_amount !== '') {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount !== '') {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date !== '') {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date !== '') {
            $query->where('date', '<=', $request->end_date);
        }

        // Search by description
        if ($request->has('search') && $request->search !== '') {
            $query->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->latest('date')->paginate(15)->withQueryString();

        // Get distinct categories for filter dropdown
        $categories = $request->user()
            ->expenses()
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('expenses.index', compact('expenses', 'categories'));
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
    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Expense::create($validated);

        return Redirect::route('expenses.index')->with('status', 'expense-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): View
    {
        // Verify ownership
        if ($expense->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        // Verify ownership
        if ($expense->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        $expense->update($validated);

        return Redirect::route('expenses.index')->with('status', 'expense-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        // Verify ownership
        if ($expense->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $expense->delete();

        return Redirect::route('expenses.index')->with('status', 'expense-deleted');
    }
}