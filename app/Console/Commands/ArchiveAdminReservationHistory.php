<?php

namespace App\Console\Commands;

use App\Services\AdminReservationArchiveService;
use Illuminate\Console\Command;

class ArchiveAdminReservationHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin-reservations:archive 
                            {--days=30 : Archive records older than this many days}
                            {--month= : Archive a specific month (YYYY-MM format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive admin reservation history records older than specified days or for a specific month';

    /**
     * Execute the console command.
     */
    public function handle(AdminReservationArchiveService $service)
    {
        $this->info('Starting admin reservation history archive process...');

        try {
            if ($this->option('month')) {
                // Archive specific month
                $month = $this->option('month');
                $this->info("Archiving records for month: {$month}");
                
                $result = $service->archiveMonth($month);
                
                if ($result['success']) {
                    $this->info("✓ {$result['message']}");
                    $this->info("  Archived {$result['count']} records.");
                } else {
                    $this->warn("✗ {$result['message']}");
                }
            } else {
                // Archive old records
                $days = (int) $this->option('days');
                $this->info("Archiving records older than {$days} days...");
                
                $result = $service->archiveOldRecords($days);
                
                if ($result['success']) {
                    $this->info("✓ {$result['message']}");
                    if ($result['count'] > 0) {
                        $this->info("  Archived {$result['count']} records across " . count($result['months']) . " month(s).");
                        $this->info("  Months: " . implode(', ', $result['months']));
                    }
                } else {
                    $this->warn("✗ {$result['message']}");
                }
            }

            $this->info('Archive process completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Archive process failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

