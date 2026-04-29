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
            $user = $request->user();
            $userId = $user->id;

            try {
                // Try indexed search (Meilisearch). If Meili is down this will throw.
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
            } catch (\Throwable $e) {
                // Fallback to simple DB LIKE search when search engine isn't available.
                $tasks = $user->tasks()
                    ->where(function ($qb) use ($q) {
                        $qb->where('title', 'like', "%{$q}%")
                           ->orWhere('description', 'like', "%{$q}%");
                    })
                    ->orderBy('due_date', 'asc')
                    ->paginate($perPage, ['*'], 'page', $page);

                $notes = $user->notes()
                    ->where(function ($qb) use ($q) {
                        $qb->where('title', 'like', "%{$q}%")
                           ->orWhere('content', 'like', "%{$q}%");
                    })
                    ->latest('updated_at')
                    ->paginate($perPage, ['*'], 'page', $page);

                $expenses = $user->expenses()
                    ->where(function ($qb) use ($q) {
                        $qb->where('description', 'like', "%{$q}%")
                           ->orWhere('category', 'like', "%{$q}%");
                    })
                    ->latest('date')
                    ->paginate($perPage, ['*'], 'page', $page);

                $budgets = $user->budgets()
                    ->where(function ($qb) use ($q) {
                        $qb->where('name', 'like', "%{$q}%")
                           ->orWhere('category', 'like', "%{$q}%");
                    })
                    ->paginate($perPage, ['*'], 'page', $page);
            }
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

            try {
                // Try searching indexed records for suggestions
                $taskTitles = Task::search($q)->where('user_id', $user->id)->take(6)->get()->pluck('title')->toArray();
                $noteTitles = Note::search($q)->where('user_id', $user->id)->take(6)->get()->pluck('title')->toArray();
                $expenseDesc = Expense::search($q)->where('user_id', $user->id)->take(6)->get()->pluck('description')->toArray();
                $categories = $user->expenses()->where('category', 'like', "%{$q}%")->distinct()->pluck('category')->toArray();

                $results = array_values(array_unique(array_merge($taskTitles, $noteTitles, $expenseDesc, $categories)));
                $results = array_slice($results, 0, 8);
            } catch (\Throwable $e) {
                // Fallback to DB queries for suggestions
                $taskTitles = $user->tasks()->where('title', 'like', "%{$q}%")->pluck('title')->toArray();
                $noteTitles = $user->notes()->where('title', 'like', "%{$q}%")->pluck('title')->toArray();
                $expenseDesc = $user->expenses()->where('description', 'like', "%{$q}%")->pluck('description')->toArray();
                $categories = $user->expenses()->where('category', 'like', "%{$q}%")->distinct()->pluck('category')->toArray();

                $results = array_values(array_unique(array_merge($taskTitles, $noteTitles, $expenseDesc, $categories)));
                $results = array_slice($results, 0, 8);
            }
        }

        return response()->json($results);
    }
}
