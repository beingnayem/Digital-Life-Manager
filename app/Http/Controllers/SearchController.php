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

        $page = max(1, (int) $request->query('page', 1));
        $perPage = 12;

        $tasks = collect();
        $notes = collect();
        $expenses = collect();
        $budgets = collect();

        if ($q !== '') {
            $userId = $request->user()->id;

            $tasks = Task::search($q)
                ->where('user_id', $userId)
                ->orderBy('due_date', 'asc')
                ->paginate($perPage, 'page', $page);

            $notes = Note::search($q)
                ->where('user_id', $userId)
                ->paginate($perPage, 'page', $page);

            $expenses = Expense::search($q)
                ->where('user_id', $userId)
                ->paginate($perPage, 'page', $page);

            $budgets = Budget::search($q)
                ->where('user_id', $userId)
                ->paginate($perPage, 'page', $page);
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
