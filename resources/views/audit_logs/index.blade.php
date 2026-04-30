@extends('layouts.app')

@section('content')
    <div class="page-container py-6 space-y-6">
        <div class="page-header">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary-500">Operations</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">Audit Logs</h1>
                <p class="mt-1 text-sm text-slate-500">Track create, update, and delete activity across the app.</p>
            </div>

            <form method="GET" class="flex w-full flex-wrap items-center gap-2 md:w-auto">
                <input name="q" value="{{ request('q') }}" placeholder="Search action, entity, IP" class="form-input w-full sm:w-auto sm:min-w-[16rem]" />
                <button class="btn-primary whitespace-nowrap">Search</button>
            </form>
        </div>

        <div class="table-shell">
            <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Details</th>
                        <th class="text-right">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at ? $log->created_at->toDayDateTimeString() : '-' }}</td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $log->action }}</span></td>
                            <td>{{ $log->entity_type }} @if($log->entity_id) (#{{ $log->entity_id }}) @endif</td>
                            <td>{{ Str::limit($log->getDescription(), 80) }}</td>
                            <td class="text-right"><a href="{{ route('audit-logs.show', $log) }}" class="font-semibold text-primary-600 hover:text-primary-700">Open</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-8 text-center text-slate-500" colspan="6">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
