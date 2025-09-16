<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AcademicSession;

class ImportAcademicSessions extends Command
{
    protected $signature = 'import:academic-sessions';
    protected $description = 'Import academic sessions from Flask database';

    public function handle()
    {
        try {
            // Truncate the academic_sessions table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('academic_sessions')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated academic_sessions table');
            Log::info('Truncated academic_sessions table');

            // Academic session data
            $sessions = [
                ['id' => 1, 'year' => '2023/2024', 'is_current' => 1, 'current_term' => 'Third'],
                ['id' => 2, 'year' => '2024/2025', 'is_current' => 0, 'current_term' => 'First'],
                ['id' => 3, 'year' => '2025/2026', 'is_current' => 0, 'current_term' => 'First'],
                ['id' => 4, 'year' => '2026/2027', 'is_current' => 0, 'current_term' => 'First'],
                ['id' => 5, 'year' => '2027/2028', 'is_current' => 0, 'current_term' => 'First'],
                ['id' => 6, 'year' => '2028/2029', 'is_current' => 0, 'current_term' => 'First'],
                ['id' => 7, 'year' => '2029/2030', 'is_current' => 0, 'current_term' => 'First'],
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE academic_sessions AUTO_INCREMENT = 1');

            // Insert academic sessions
            foreach ($sessions as $session) {
                DB::table('academic_sessions')->insert([
                    'id' => $session['id'],
                    'year' => $session['year'],
                    'is_current' => $session['is_current'],
                    'current_term' => $session['current_term'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported academic session: {$session['year']} (ID: {$session['id']})");
                Log::info("Imported academic session: {$session['year']} (ID: {$session['id']})");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($sessions, 'id'));
            DB::statement("ALTER TABLE academic_sessions AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Academic sessions imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing academic sessions: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
