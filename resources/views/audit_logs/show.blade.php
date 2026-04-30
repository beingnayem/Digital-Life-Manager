@extends('layouts.app')

@section('content')
    <div class="page-container py-6">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold">Audit Log Details</h1>
            <a href="{{ route('audit-logs.index') }}" class="btn-secondary">Back</a>
        </div>

        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs text-slate-500">When</dt>
                    <dd class="mt-1">{{ $auditLog->created_at ? $auditLog->created_at->toDayDateTimeString() : '-' }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-slate-500">User</dt>
                    <dd class="mt-1">{{ $auditLog->user->name ?? ($auditLog->ip_address ?? 'System') }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-slate-500">Action</dt>
                    <dd class="mt-1">{{ $auditLog->action }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-slate-500">Entity</dt>
                    <dd class="mt-1">{{ $auditLog->entity_type }} @if($auditLog->entity_id) (#{{ $auditLog->entity_id }}) @endif</dd>
                </div>
            </dl>

            <div class="mt-6">
                <h3 class="text-sm font-medium">Old Values</h3>
                <pre class="mt-2 p-3 bg-slate-50 rounded">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-medium">New Values</h3>
                <pre class="mt-2 p-3 bg-slate-50 rounded">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>

            <div class="mt-6 text-sm text-slate-500">IP: {{ $auditLog->ip_address ?? 'N/A' }} · Agent: {{ Str::limit($auditLog->user_agent, 120) ?? 'N/A' }}</div>
        </div>
    </div>
@endsection
