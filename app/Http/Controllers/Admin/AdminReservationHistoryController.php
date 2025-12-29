<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminReservationHistory;
use App\Models\AdminReservationArchive;
use App\Services\AdminReservationArchiveService;
use App\DataTables\AdminReservationHistoryDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReservationHistoryController extends Controller
{
    /**
     * Display a listing of admin reservation history (last 30 days).
     */
    public function index(Request $request, AdminReservationHistoryDataTable $dataTable)
    {
        // Only super admin can view admin reservation history
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can view admin reservation history.');
        }

        return $dataTable->render('admin.reservation-history.index');
    }

    /**
     * Archive old admin reservation history records
     */
    public function archive(Request $request, AdminReservationArchiveService $service)
    {
        // Only super admin can archive
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can archive admin reservation history.');
        }

        $request->validate([
            'archive_month' => 'required|date_format:Y-m',
        ]);

        try {
            $result = $service->archiveMonth($request->archive_month);
            
            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error archiving records: ' . $e->getMessage());
        }
    }

    /**
     * View archived records
     */
    public function viewArchive(Request $request, string $month)
    {
        // Only super admin can view archives
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can view archived records.');
        }

        $archivedRecords = AdminReservationArchive::where('archive_month', $month)
            ->with(['reservation', 'admin'])
            ->orderBy('action_at', 'desc')
            ->get();

        return view('admin.reservation-history.archive', compact('archivedRecords', 'month'));
    }

    /**
     * List all archive months
     */
    public function listArchives()
    {
        // Only super admin can view archives
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can view archived records.');
        }

        $archiveMonths = AdminReservationArchive::select('archive_month')
            ->distinct()
            ->orderBy('archive_month', 'desc')
            ->pluck('archive_month');

        // Get count for each month
        $archiveData = [];
        foreach ($archiveMonths as $month) {
            $archiveData[] = [
                'month' => $month,
                'count' => AdminReservationArchive::where('archive_month', $month)->count(),
            ];
        }

        return view('admin.reservation-history.archives', compact('archiveData'));
    }

    /**
     * Clear archive (delete archived records)
     */
    public function clearArchive(Request $request, string $month, AdminReservationArchiveService $service)
    {
        // Only super admin can clear archives
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can clear archived records.');
        }

        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        try {
            $count = $service->clearArchive($month);
            
            return redirect()->route('admin.reservation-history.archives')
                ->with('success', "Successfully cleared {$count} archived records for {$month}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing archive: ' . $e->getMessage());
        }
    }
}

