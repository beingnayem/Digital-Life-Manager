<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Note;
use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Global search results page.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $tasks = collect();
        $notes = collect();
        $expenses = collect();
        $budgets = collect();

        if ($q !== '') {
            $user = $request->user();

            $tasks = $user->tasks()
                ->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('title', 'like', "%{$q}%")
                             ->orWhere('description', 'like', "%{$q}%");
                })
                ->latest()
                ->take(8)
                ->get();

            $notes = $user->notes()
                ->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('title', 'like', "%{$q}%")
                             ->orWhere('content', 'like', "%{$q}%");
                })
                ->latest()
                ->take(8)
                ->get();

            $expenses = $user->expenses()
                ->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('description', 'like', "%{$q}%")
                             ->orWhere('category', 'like', "%{$q}%");
                })
                ->latest()
                ->take(8)
                ->get();

            $budgets = $user->budgets()
                ->where('name', 'like', "%{$q}%")
                ->take(6)
                ->get();
        }

        return view('search.results', compact('q', 'tasks', 'notes', 'expenses', 'budgets'));
    }

    /**
     * Autocomplete suggestions (JSON).
     */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $results = [];

        if ($q !== '' && $request->user()) {
            $user = $request->user();

            $taskTitles = $user->tasks()->where('title', 'like', "%{$q}%")->pluck('title')->toArray();
            $noteTitles = $user->notes()->where('title', 'like', "%{$q}%")->pluck('title')->toArray();
            $expenseDesc = $user->expenses()->where('description', 'like', "%{$q}%")->pluck('description')->toArray();
            $categories = $user->expenses()->where('category', 'like', "%{$q}%")->distinct()->pluck('category')->toArray();

            $results = array_values(array_unique(array_merge($taskTitles, $noteTitles, $expenseDesc, $categories)));
            $results = array_slice($results, 0, 8);
        }

        return response()->json($results);
    }
}
