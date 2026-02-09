<?php

namespace App\Http\Controllers\Private\Admin;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Enums\RoleName;

class AuditController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorizeAdminOnly();

        $query = AuditLog::with(['actorable', 'auditable'])
            ->latest('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('entity', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('actor_id')) {
            $query->where('actorable_id', $request->input('actor_id'))
                  ->where('actorable_type', \App\Models\User::class);
        }

        $logs = $query->paginate(15)->withQueryString();

        return Inertia::render('Private/Admin/Audit/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'action', 'actor_id']),
            'actions' => AuditLog::select('action')->distinct()->pluck('action'), // For filter dropdown
        ]);
    }

    protected function authorizeAdminOnly(): void
    {
        $user = request()->user();
        if (!$user || !$user->hasRole(RoleName::Admin->value)) {
            abort(403, 'Solo administradores pueden ver los registros de auditoría.');
        }
    }
}
