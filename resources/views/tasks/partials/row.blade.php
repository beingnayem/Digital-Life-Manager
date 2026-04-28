<tr data-task-id="{{ $task->id }}" class="hover:bg-slate-50">
    <td class="px-4 py-3">
        <form method="POST" action="{{ route('tasks.update', $task) }}" data-ajax-row-form="task" data-record-id="{{ $task->id }}" data-record-action="toggle">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'not_started' : 'completed' }}">
            <button type="submit" class="inline-flex h-6 w-6 items-center justify-center rounded-md border {{ $task->status === 'completed' ? 'bg-green-600' : 'bg-white' }}">
                @if ($task->status === 'completed')
                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L9 14.414l-3.707-3.707a1 1 0 00-1.414 1.414l4.414 4.414a1 1 0 001.414 0l8.414-8.414a1 1 0 00-1.414-1.414L9 12.586 6.293 9.879A1 1 0 004.879 11.293L9 15.414l7.707-7.707a1 1 0 00-1.414-1.414z" clip-rule="evenodd"/></svg>
                @endif
            </button>
        </form>
    </td>
    <td class="px-4 py-3">
        <div class="min-w-0">
            <p class="truncate font-medium text-slate-900">{{ $task->title }}</p>
            <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</p>
        </div>
    </td>
    <td class="px-4 py-3 text-sm text-slate-600">{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
    <td class="px-4 py-3 text-sm">
        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $task->priority === 'high' ? 'bg-red-50 text-red-700' : ($task->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($task->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-slate-100 text-slate-700'))}}">{{ ucfirst($task->priority) }}</span>
    </td>
    <td class="px-4 py-3 text-sm text-slate-600">{{ ucwords(str_replace('_', ' ', $task->status)) }}</td>
    <td class="px-4 py-3 text-right text-sm font-medium">
        <button x-data @click="$dispatch('open-task-modal', { mode: 'edit', task: {{ json_encode($task) }} })" class="mr-3 text-primary-600 hover:text-primary-700">Edit</button>
        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" data-ajax-row-form="task" data-record-id="{{ $task->id }}" data-record-action="delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
        </form>
    </td>
</tr>
