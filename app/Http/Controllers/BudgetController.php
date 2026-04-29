<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgets = $request->user()->budgets()
            ->orderBy('month_year', 'desc')
            ->paginate(10);

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('budgets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'limit_amount' => ['required', 'numeric', 'min:0.01'],
            'month_year' => ['required', 'date_format:Y-m'],
            'alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $validated['user_id'] = $request->user()->id;

        Budget::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'category' => $validated['category'],
                'month_year' => $validated['month_year'],
            ],
            $validated
        );

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Budget created or updated successfully.');
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        return view('budgets.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'limit_amount' => ['required', 'numeric', 'min:0.01'],
            'alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $budget->update($validated);

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Budget deleted successfully.');
    }
}
