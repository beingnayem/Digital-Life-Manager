<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Task Manager</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center gap-2">
                    <select name="priority" class="form-input min-w-[11rem] text-sm">
                        <option value="">All priorities</option>
                        <option value="low" {{ request('priority')=='low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority')=='medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority')=='high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority')=='urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>

                    <select name="status" class="form-input text-sm">
                        <option value="">All status</option>
                        <option value="not_started" {{ request('status')=='not_started' ? 'selected' : '' }}>Not started</option>
                        <option value="in_progress" {{ request('status')=='in_progress' ? 'selected' : '' }}>In progress</option>
                        <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <input name="search" value="{{ request('search') }}" placeholder="Search title" class="form-input text-sm px-3" />
                    <button type="submit" class="btn-secondary">Filter</button>
                </form>

                <button x-data @click="$dispatch('open-task-modal', { mode: 'create' })" class="btn-primary">+ New Task</button>
            </div>
        </div>
    </x-slot>

    <div class="page">
        <div class="page-container">
            <div class="card">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Done</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Due</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Priority</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tasks-table-body" class="divide-y divide-slate-100 bg-white">
                                @forelse ($tasks as $task)
                                    @include('tasks.partials.row', ['task' => $task])
                                @empty
                                    <tr data-empty-state>
                                        <td colspan="6" class="px-4 py-10">
                                            <x-empty-state
                                                title="No tasks yet"
                                                description="Create your first task to start planning your day and tracking progress."
                                            >
                                                <button x-data @click="$dispatch('open-task-modal', { mode: 'create' })" class="btn-primary">+ New Task</button>
                                            </x-empty-state>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $tasks->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Task Modal (create/edit) --}}
    <div x-data x-cloak @open-task-modal.window="(e) => { $store.taskModal.open = true; $store.taskModal.mode = e.detail.mode; if(e.detail.mode === 'edit') { $store.taskModal.task = e.detail.task; } else { $store.taskModal.task = { title: '', description: '', priority: 'medium', status: 'not_started', due_date: '' }; } }">
        <div x-show="$store.taskModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40" @click="$store.taskModal.open=false"></div>
            <div class="relative w-full max-w-2xl">
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-slate-900" x-text="$store.taskModal.mode === 'create' ? 'Add Task' : 'Edit Task'"></h3>
                            <button @click="$store.taskModal.open=false" class="text-slate-500">✕</button>
                        </div>

                        <form :action="$store.taskModal.mode === 'create' ? '{{ route('tasks.store') }}' : '/tasks/' + ($store.taskModal.task.id ?? '')" method="POST" class="mt-4 space-y-4" data-ajax-form="task">
                            @csrf
                            <template x-if="$store.taskModal.mode === 'edit'">
                                <input type="hidden" name="_method" value="PATCH">
                            </template>

                            <div>
                                <label class="form-label">Title</label>
                                <input name="title" x-model="$store.taskModal.task.title" class="form-input w-full" required />
                            </div>

                            <div>
                                <label class="form-label">Due date</label>
                                <input name="due_date" type="date" x-model="$store.taskModal.task.due_date" class="form-input" />
                            </div>

                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" x-model="$store.taskModal.task.priority" class="form-input w-full">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Status</label>
                                    <select name="status" x-model="$store.taskModal.task.status" class="form-input w-48">
                                        <option value="not_started">Not started</option>
                                        <option value="in_progress">In progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Description</label>
                                <textarea name="description" x-model="$store.taskModal.task.description" class="form-input w-full" rows="4"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <button type="button" @click="$store.taskModal.open=false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary" data-loading-label="Saving..." x-text="$store.taskModal.mode === 'create' ? 'Create' : 'Save'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('taskModal', {
                open: false,
                mode: 'create',
                task: { }
            });
        });
    </script>
</x-app-layout>
