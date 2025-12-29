<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use App\DataTables\ActivityLogsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request, ActivityLogsDataTable $dataTable)
    {
        // Only super admin can view activity logs
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can view activity logs.');
        }

        return $dataTable->render('admin.activity-logs.index');
    }

    /**
     * Display activity logs for a specific user
     */
    public function userLogs(string $userId, ActivityLogsDataTable $dataTable)
    {
        $user = Auth::user();
        $targetUser = User::findOrFail($userId);

        // Super admin can view all logs
        // Hotel owners can view their staff's logs
        if (!$user->isSuperAdmin()) {
            if (!$user->isHotelOwner() || $targetUser->parent_user_id !== $user->id) {
                abort(403, 'You do not have permission to view this user\'s activity logs.');
            }
        }

        return $dataTable->with('user_id', $userId)->render('admin.activity-logs.user', compact('targetUser'));
    }
}

