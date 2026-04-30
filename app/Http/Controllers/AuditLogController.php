<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a paginated list of audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('action', 'like', "%{$q}%")
                  ->orWhere('entity_type', 'like', "%{$q}%")
                  ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('audit_logs.index', compact('logs'));
    }

    /**
     * Display a single audit log entry.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('audit_logs.show', compact('auditLog'));
    }
}
