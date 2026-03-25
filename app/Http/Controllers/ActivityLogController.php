<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = $request->user()->role === 'admin';

        $baseQuery = ActivityLog::with('user')
            ->when($isAdmin, function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->whereIn('role', ['supplier', 'dapur']);
                });
            });

        $logs = (clone $baseQuery)
            ->when($request->filled('action'), function ($query) use ($request) {
                $query->where('action', $request->string('action'));
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->integer('user_id'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = User::query()
            ->when($isAdmin, function ($query) {
                $query->whereIn('role', ['supplier', 'dapur']);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $actions = (clone $baseQuery)
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('activity_logs.index', compact('logs', 'users', 'actions'));
    }
}
