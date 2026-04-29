<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Edit Task</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="btn-secondary">Back to Tasks</a>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="card mx-auto max-w-3xl">
                <div class="card-body">
                    <div class="mb-6 flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                        <div>
                            <p class="text-sm text-slate-500">Update the task details and save your changes.</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-900">{{ $task->title }}</h2>
                        </div>
                        <a href="{{ route('dashboard') }}" class="link text-sm">Dashboard</a>
                    </div>

                    <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="form-label" for="title">Title</label>
                            <input id="title" name="title" type="text" value="{{ old('title', $task->title) }}" class="form-input w-full" required />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-input w-full" rows="5">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="due_date">Due date</label>
                                <input id="due_date" name="due_date" type="date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="form-input w-full" />
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="category">Category</label>
                                <input id="category" name="category" type="text" value="{{ old('category', $task->category) }}" class="form-input w-full" />
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="priority">Priority</label>
                                <select id="priority" name="priority" class="form-input w-full" required>
                                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('priority', $task->priority) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="status">Status</label>
                                <select id="status" name="status" class="form-input w-full" required>
                                    @foreach (['not_started' => 'Not started', 'in_progress' => 'In progress', 'completed' => 'Completed', 'archived' => 'Archived', 'cancelled' => 'Cancelled'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="form-label" for="estimated_hours">Estimated hours</label>
                                <input id="estimated_hours" name="estimated_hours" type="number" min="0" step="1" value="{{ old('estimated_hours', $task->estimated_hours) }}" class="form-input w-full" />
                                @error('estimated_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="actual_hours">Actual hours</label>
                                <input id="actual_hours" name="actual_hours" type="number" min="0" step="1" value="{{ old('actual_hours', $task->actual_hours) }}" class="form-input w-full" />
                                @error('actual_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <a href="{{ route('tasks.index') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">Save Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
