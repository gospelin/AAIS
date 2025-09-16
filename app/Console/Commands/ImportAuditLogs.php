<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportAuditLogs extends Command
{
    protected $signature = 'import:audit-logs';
    protected $description = 'Import audit logs from Flask database';

    public function handle()
    {
        try {
            // Truncate the audit_log table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('audit_log')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated audit_log table');
            Log::info('Truncated audit_log table');

            // Audit log data
            $logs = [
                ['id' => 1, 'user_id' => 1, 'action' => 'Logged out', 'timestamp' => '2025-03-02 16:04:40'],
                ['id' => 2, 'user_id' => 1, 'action' => 'Logged in', 'timestamp' => '2025-03-02 16:04:58'],
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE audit_log AUTO_INCREMENT = 1');

            // Insert audit logs
            foreach ($logs as $log) {
                DB::table('audit_log')->insert([
                    'id' => $log['id'],
                    'user_id' => $log['user_id'],
                    'action' => $log['action'],
                    'timestamp' => $log['timestamp'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported audit log for user_id: {$log['user_id']} (ID: {$log['id']})");
                Log::info("Imported audit log for user_id: {$log['user_id']} (ID: {$log['id']})");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($logs, 'id'));
            DB::statement("ALTER TABLE audit_log AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Audit logs imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing audit logs: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
