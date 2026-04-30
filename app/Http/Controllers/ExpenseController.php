<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
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
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', (float) $request->input('min_amount'));
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', (float) $request->input('max_amount'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->input('end_date'));
        }

        // Search by description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $expenses = $query->latest('date')->paginate(15)->withQueryString();

        // Get distinct categories for filter dropdown
        $categories = $request->user()
            ->expenses()
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        // Calculate chart data
        $chartData = [
            'trend_30day' => $this->get30DayTrend($request->user()),
            'category_breakdown' => $this->getCategoryBreakdown($request->user()),
            'status_breakdown' => $this->getStatusBreakdown($request->user()),
            'payment_method_breakdown' => $this->getPaymentMethodBreakdown($request->user()),
        ];

        return view('expenses.index', compact('expenses', 'categories', 'chartData'));
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
    public function store(ExpenseRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $expense = Expense::create($validated);

        if ($request->expectsJson()) {
            $expense->refresh();

            return response()->json([
                'message' => 'expense-created',
                'expense' => $expense,
                'row_html' => view('expenses.partials.row', ['expense' => $expense])->render(),
            ]);
        }

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
    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse|JsonResponse
    {
        // Verify ownership
        if ($expense->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        $expense->update($validated);

        if ($request->expectsJson()) {
            $expense->refresh();

            return response()->json([
                'message' => 'expense-updated',
                'expense' => $expense,
                'row_html' => view('expenses.partials.row', ['expense' => $expense])->render(),
            ]);
        }

        return Redirect::route('expenses.index')->with('status', 'expense-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Expense $expense): RedirectResponse|JsonResponse
    {
        // Verify ownership
        if ($expense->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $expenseId = $expense->id;
        $expense->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'expense-deleted',
                'expense_id' => $expenseId,
            ]);
        }

        return Redirect::route('expenses.index')->with('status', 'expense-deleted');
    }

    /**
     * Get 30-day expense trend data for charting.
     */
    private function get30DayTrend($user)
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $amount = (float) $user->expenses()
                ->confirmed()
                ->whereDate('date', $date)
                ->sum('amount');
            
            if ($i % 5 === 0 || $i === 0) {  // Show every 5 days to avoid clutter
                $data[] = [
                    'label' => $date->format('M d'),
                    'value' => round($amount, 2),
                ];
            }
        }
        return $data;
    }

    /**
     * Get expense breakdown by category.
     */
    private function getCategoryBreakdown($user)
    {
        $categories = $user->expenses()
            ->confirmed()
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->orderByDesc('total')
            ->get()
            ->take(8);

        return $categories->map(fn($cat) => [
            'label' => $cat->category,
            'value' => round((float)$cat->total, 2),
        ])->values();
    }

    /**
     * Get expense breakdown by status.
     */
    private function getStatusBreakdown($user)
    {
        $statuses = ['confirmed', 'pending', 'disputed', 'refunded'];
        $statusColors = [
            'confirmed' => '#10b981',
            'pending' => '#f59e0b',
            'disputed' => '#ef4444',
            'refunded' => '#6b7280',
        ];

        $data = [];
        foreach ($statuses as $status) {
            $amount = (float) $user->expenses()
                ->where('status', $status)
                ->sum('amount');
            
            if ($amount > 0) {
                $data[] = [
                    'label' => ucfirst($status),
                    'value' => round($amount, 2),
                    'color' => $statusColors[$status],
                ];
            }
        }

        return $data;
    }

    /**
     * Get expense breakdown by payment method.
     */
    private function getPaymentMethodBreakdown($user)
    {
        $paymentMethods = $user->expenses()
            ->confirmed()
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->orderByDesc('total')
            ->get();

        $methodColors = [
            'cash' => '#16a34a',
            'card' => '#2563eb',
            'bank_transfer' => '#7c3aed',
            'mobile_payment' => '#db2777',
            'check' => '#f59e0b',
            'other' => '#64748b',
        ];

        return $paymentMethods->map(function ($method) use ($methodColors) {
            return [
                'label' => ucwords(str_replace('_', ' ', (string) $method->payment_method)),
                'value' => round((float) $method->total, 2),
                'color' => $methodColors[$method->payment_method] ?? '#94a3b8',
            ];
        })->values();
    }
}