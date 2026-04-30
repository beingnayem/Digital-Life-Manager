@extends('layouts.app')

@section('content')
    <div class="page-container py-6">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold">Audit Log Details</h1>
            <a href="{{ route('audit-logs.index') }}" class="btn-secondary">Back</a>
        </div>

        <div class="mt-6 bg-white shadow rounded-lg p-6">
            @php
                $changes = $auditLog->getChanges() ?? [];

                $formatField = function (string $field): string {
                    return Str::of($field)->replace('_', ' ')->title()->toString();
                };

                $formatValue = function ($value): string {
                    if ($value === null || $value === '') {
                        return 'Not set';
                    }

                    if (is_bool($value)) {
                        return $value ? 'Yes' : 'No';
                    }

                    if (is_array($value)) {
                        if ($value === []) {
                            return 'Empty';
                        }

                        return implode(', ', array_map(static function ($item): string {
                            if (is_bool($item)) {
                                return $item ? 'Yes' : 'No';
                            }

                            if ($item === null || $item === '') {
                                return 'Not set';
                            }

                            return (string) $item;
                        }, $value));
                    }

                    if (is_float($value)) {
                        return number_format($value, 2);
                    }

                    return (string) $value;
                };
            @endphp

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
                    <dd class="mt-1">{{ Str::title($auditLog->action) }}</dd>
                </div>

                <div>
                    <dt class="text-xs text-slate-500">Entity</dt>
                    <dd class="mt-1">{{ $auditLog->entity_type }} @if($auditLog->entity_id) (#{{ $auditLog->entity_id }}) @endif</dd>
                </div>
            </dl>

            <div class="mt-6">
                <h3 class="text-sm font-medium">What Changed</h3>

                @if (empty($changes))
                    <div class="mt-2 rounded bg-slate-50 p-3 text-sm text-slate-600">
                        No field-level changes were recorded for this activity.
                    </div>
                @else
                    <div class="mt-2 overflow-x-auto rounded border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Field</th>
                                    <th class="px-4 py-3">Before</th>
                                    <th class="px-4 py-3">After</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                @foreach ($changes as $field => $change)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $formatField($field) }}</td>
                                        <td class="px-4 py-3">{{ $formatValue($change['from'] ?? null) }}</td>
                                        <td class="px-4 py-3">{{ $formatValue($change['to'] ?? null) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <details class="mt-4 rounded border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">
                    <summary class="cursor-pointer font-medium text-slate-700">Show technical JSON details</summary>
                    <div class="mt-3 grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Old Values</h4>
                            <pre class="mt-2 overflow-auto rounded bg-white p-3 text-xs">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500">New Values</h4>
                            <pre class="mt-2 overflow-auto rounded bg-white p-3 text-xs">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                </details>
            </div>

            <div class="mt-6 text-sm text-slate-500">IP: {{ $auditLog->ip_address ?? 'N/A' }} · Agent: {{ Str::limit($auditLog->user_agent, 120) ?? 'N/A' }}</div>
        </div>
    </div>
@endsection
