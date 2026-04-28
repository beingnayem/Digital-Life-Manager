<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
 use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = $request->user()->tasks();

        // Filters
        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $task = Task::create($validated);

        if ($request->expectsJson()) {
            $task->refresh();

            return response()->json([
                'message' => 'task-created',
                'task' => $task,
                'row_html' => view('tasks.partials.row', ['task' => $task])->render(),
            ]);
        }

        return Redirect::route('tasks.index')->with('status', 'task-created');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Task $task): View
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task): RedirectResponse|JsonResponse
    {

        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validated();
        $task->update($validated);

        if ($request->expectsJson()) {
            $task->refresh();

            return response()->json([
                'message' => 'task-updated',
                'task' => $task,
                'row_html' => view('tasks.partials.row', ['task' => $task])->render(),
            ]);
        }

        return Redirect::route('tasks.index')->with('status', 'task-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403);
        }

        $taskId = $task->id;
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'task-deleted',
                'task_id' => $taskId,
            ]);
        }

        return Redirect::route('tasks.index')->with('status', 'task-deleted');
    }
}