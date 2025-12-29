<?php

namespace App\Services;

use App\Models\AdminReservationHistory;
use App\Models\AdminReservationArchive;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReservationArchiveService
{
    /**
     * Archive records for a specific month
     */
    public function archiveMonth(string $month): array
    {
        // Validate month format
        try {
            $date = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid month format. Expected YYYY-MM, got: {$month}");
        }

        // Check if already archived
        $existingArchive = AdminReservationArchive::where('archive_month', $month)->exists();
        if ($existingArchive) {
            throw new \Exception("Archive for {$month} already exists.");
        }

        DB::beginTransaction();
        try {
            // Get all history records for the month that are not already archived
            $historyRecords = AdminReservationHistory::whereNull('archive_month')
                ->whereYear('action_at', $date->year)
                ->whereMonth('action_at', $date->month)
                ->with(['reservation', 'admin'])
                ->get();

            if ($historyRecords->isEmpty()) {
                return [
                    'success' => false,
                    'message' => "No records found to archive for {$month}.",
                    'count' => 0,
                ];
            }

            $archivedCount = 0;
            foreach ($historyRecords as $history) {
                // Create archive record
                AdminReservationArchive::create([
                    'original_history_id' => $history->id,
                    'reservation_id' => $history->reservation_id,
                    'room_id' => $history->reservation->room_id ?? null,
                    'hotel_id' => $history->reservation->hotel_id ?? null,
                    'admin_id' => $history->admin_id,
                    'action_type' => $history->action_type,
                    'action_at' => $history->action_at,
                    'archive_month' => $month,
                    'archived_at' => now(),
                    'notes' => $history->notes,
                ]);

                // Mark as archived
                $history->update(['archive_month' => $month]);
                $archivedCount++;
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Successfully archived {$archivedCount} records for {$month}.",
                'count' => $archivedCount,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Archive records older than specified days (default 30)
     */
    public function archiveOldRecords(int $daysOld = 30): array
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        $cutoffMonth = $cutoffDate->format('Y-m');

        // Get all months that need archiving
        $monthsToArchive = AdminReservationHistory::whereNull('archive_month')
            ->where('action_at', '<', $cutoffDate)
            ->selectRaw('DATE_FORMAT(action_at, "%Y-%m") as month')
            ->distinct()
            ->pluck('month')
            ->sort()
            ->values();

        if ($monthsToArchive->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No records found to archive.',
                'count' => 0,
                'months' => [],
            ];
        }

        $totalArchived = 0;
        $archivedMonths = [];

        foreach ($monthsToArchive as $month) {
            try {
                $result = $this->archiveMonth($month);
                if ($result['success']) {
                    $totalArchived += $result['count'];
                    $archivedMonths[] = $month;
                }
            } catch (\Exception $e) {
                // Log error but continue with other months
                \Log::error("Failed to archive month {$month}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'message' => "Archived {$totalArchived} records across " . count($archivedMonths) . " month(s).",
            'count' => $totalArchived,
            'months' => $archivedMonths,
        ];
    }

    /**
     * Clear archive for a specific month
     */
    public function clearArchive(string $month): int
    {
        DB::beginTransaction();
        try {
            $count = AdminReservationArchive::where('archive_month', $month)->count();
            
            // Delete archived records
            AdminReservationArchive::where('archive_month', $month)->delete();
            
            // Also remove archive_month from history records
            AdminReservationHistory::where('archive_month', $month)->update(['archive_month' => null]);

            DB::commit();

            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

