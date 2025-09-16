<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportClasses extends Command
{
    protected $signature = 'import:classes';
    protected $description = 'Import classes from Flask database';

    public function handle()
    {
        try {
            // Truncate the classes table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('classes')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated classes table');
            Log::info('Truncated classes table');

            // Classes data
            $classes = [
                ['id' => 1, 'name' => 'Pre-Nursery', 'section' => 'Nursery', 'hierarchy' => 2],
                ['id' => 2, 'name' => 'Creche', 'section' => 'Nursery', 'hierarchy' => 1],
                ['id' => 3, 'name' => 'Nursery 3', 'section' => 'Nursery', 'hierarchy' => 5],
                ['id' => 4, 'name' => 'Nursery 1', 'section' => 'Nursery', 'hierarchy' => 3],
                ['id' => 5, 'name' => 'JSS 2', 'section' => 'Secondary', 'hierarchy' => 12],
                ['id' => 6, 'name' => 'Nursery 2', 'section' => 'Nursery', 'hierarchy' => 4],
                ['id' => 7, 'name' => 'Basic 1', 'section' => 'Primary', 'hierarchy' => 6],
                ['id' => 8, 'name' => 'Basic 2', 'section' => 'Primary', 'hierarchy' => 7],
                ['id' => 9, 'name' => 'Basic 3', 'section' => 'Primary', 'hierarchy' => 8],
                ['id' => 10, 'name' => 'JSS 1', 'section' => 'Secondary', 'hierarchy' => 11],
                ['id' => 11, 'name' => 'Basic 5', 'section' => 'Primary', 'hierarchy' => 10],
                ['id' => 12, 'name' => 'JSS 3', 'section' => 'Secondary', 'hierarchy' => 13],
                ['id' => 13, 'name' => 'Basic 4', 'section' => 'Primary', 'hierarchy' => 9],
                ['id' => 14, 'name' => 'SSS', 'section' => 'Senior Secondary', 'hierarchy' => 14],
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE classes AUTO_INCREMENT = 1');

            // Insert classes
            foreach ($classes as $class) {
                DB::table('classes')->insert([
                    'id' => $class['id'],
                    'name' => $class['name'],
                    'section' => $class['section'],
                    'hierarchy' => $class['hierarchy'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported class: {$class['name']} (ID: {$class['id']})");
                Log::info("Imported class: {$class['name']} (ID: {$class['id']})");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($classes, 'id'));
            DB::statement("ALTER TABLE classes AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Classes imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing classes: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
