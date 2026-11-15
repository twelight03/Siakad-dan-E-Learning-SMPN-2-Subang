<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, [10, 20, 25, 50, 100])) {
            $perPage = 20;
        }

        $logs = $query->latest('id')->paginate($perPage)->withQueryString();

        $users = User::orderBy('name')->get();
        $actions = ['login', 'create', 'update', 'delete', 'password_change'];
        $entityTypes = ActivityLog::distinct()->whereNotNull('entity_type')->pluck('entity_type');

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions', 'entityTypes'));
    }
}
