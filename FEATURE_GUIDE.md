# Step-by-Step: Creating Your First Feature

This guide walks you through creating a complete feature in Digital Life Manager using MVC architecture.

---

## 📋 Example: Creating a Tasks Management Feature

We'll create a tasks feature where authenticated users can create, read, update, and delete tasks.

---

## Step 1: Create Model & Migration

```bash
php artisan make:model Task -m
```

This creates:
- `app/Models/Task.php` (Model)
- `database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php` (Migration)

---

## Step 2: Define Database Schema

Edit: `database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('completed')->default(false);
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

---

## Step 3: Update the Model

Edit: `app/Models/Task.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'completed',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed' => 'boolean',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only incomplete tasks
     */
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Scope: Get tasks by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
```

Also update `app/Models/User.php` to add the relationship:

```php
<?php

namespace App\Models;

// ... other code ...

class User extends AbstractUser
{
    // ... other code ...

    /**
     * Get the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
```

---

## Step 4: Create Form Request (Validation)

```bash
php artisan make:request StoreTaskRequest
php artisan make:request UpdateTaskRequest
```

Edit: `app/Http/Requests/StoreTaskRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date_format:Y-m-d H:i',
        ];
    }
}
```

Edit: `app/Http/Requests/UpdateTaskRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date_format:Y-m-d H:i',
            'completed' => 'nullable|boolean',
        ];
    }
}
```

---

## Step 5: Create Controller

```bash
php artisan make:controller TaskController --model=Task
```

Edit: `app/Http/Controllers/TaskController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(): View
    {
        $tasks = auth()->user()->tasks()->latest()->get();
        
        return view('tasks.index', [
            'tasks' => $tasks,
            'completedCount' => $tasks->where('completed', true)->count(),
            'pendingCount' => $tasks->where('completed', false)->count(),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in database.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        auth()->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', ['task' => $task]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', ['task' => $task]);
    }

    /**
     * Update the specified task in database.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from database.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
```

---

## Step 6: Create Routes

Edit: `routes/web.php`

Add after the auth routes:

```php
Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class);
    
    // Toggle task completion status
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])
        ->name('tasks.toggle');
});
```

---

## Step 7: Create Blade Components & Views

### Create Directory
```bash
mkdir -p resources/views/tasks
```

### Task List View
Create: `resources/views/tasks/index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ $message }}
                </div>
            @endif

            <div class="mb-4 flex justify-between items-center">
                <div class="flex gap-4">
                    <div class="bg-blue-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold">{{ $completedCount }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded">
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                    </div>
                </div>
                <a href="{{ route('tasks.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + New Task
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($tasks as $task)
                            <tr class="{{ $task->completed ? 'bg-gray-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ route('tasks.show', $task) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {!! $task->completed ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded">Completed</span>' : '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">Pending</span>' !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $task->due_date?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <a href="{{ route('tasks.edit', $task) }}" 
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form method="POST" action="{{ route('tasks.destroy', $task) }}" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No tasks yet. <a href="{{ route('tasks.create') }}" class="text-blue-600">Create one.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Create Task Form
Create: `resources/views/tasks/create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               required>
                        @error('title')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="mb-4">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date') }}"
                               class="mt-1 block w-full rounded-md border-gray-300">
                        @error('due_date')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Create Task
                        </button>
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## Step 8: Run Migrations

```bash
php artisan migrate
```

---

## Step 9: Test Your Feature

1. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```

2. Login to your application

3. Navigate to `/tasks`

4. Create, edit, view, and delete tasks

---

## 🎯 MVC Flow Diagram

```
USER REQUEST
    ↓
ROUTE (routes/web.php)
    ↓
CONTROLLER (app/Http/Controllers/TaskController.php)
    ↓
    ├─→ VALIDATION (app/Http/Requests/StoreTaskRequest.php)
    │
    └─→ MODEL (app/Models/Task.php)
           ↓
        DATABASE
    ↓
VIEW (resources/views/tasks/index.blade.php)
    ↓
RESPONSE TO USER
```

---

## ✅ Summary

You've created a complete CRUD feature:
- ✅ **Model** - Database representation (Task.php)
- ✅ **Migration** - Table schema (xxxx_create_tasks_table.php)
- ✅ **Controller** - Business logic (TaskController.php)
- ✅ **Routes** - URL endpoints (routes/web.php)
- ✅ **Requests** - Validation (StoreTaskRequest.php, UpdateTaskRequest.php)
- ✅ **Views** - User interface (resources/views/tasks/*.blade.php)

Now you can extend this pattern to create more features! 🚀
