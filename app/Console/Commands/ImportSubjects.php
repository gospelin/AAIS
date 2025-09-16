<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSubjects extends Command
{
    protected $signature = 'import:subjects';
    protected $description = 'Import subjects from Flask database';

    public function handle()
    {
        try {
            // Truncate the subject table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('subjects')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated subjects table');
            Log::info('Truncated subjects table');

            // Subject data
            $subjects = [
                ['id' => 41, 'name' => 'Number Work/Quantitative', 'section' => 'Nursery', 'deactivated' => 0],
                ['id' => 44, 'name' => 'Letter Work/Verbal', 'section' => 'Nursery', 'deactivated' => 0],
                ['id' => 49, 'name' => 'Business Studies', 'section' => 'Secondary', 'deactivated' => 0],
                ['id' => 59, 'name' => 'Oral reading', 'section' => 'Nursery', 'deactivated' => 1],
                ['id' => 72, 'name' => 'Vocational Studies', 'section' => 'Nursery', 'deactivated' => 0],
                ['id' => 112, 'name' => 'Oral English', 'section' => 'Basic', 'deactivated' => 1],
                ['id' => 113, 'name' => 'Reading / Dictation', 'section' => 'Basic', 'deactivated' => 1],
                ['id' => 117, 'name' => 'Social Habit', 'section' => 'Nursery', 'deactivated' => 0],
                ['id' => 118, 'name' => 'Nursery Science', 'section' => 'Nursery', 'deactivated' => 0],
                ['id' => 120, 'name' => 'Speech work', 'section' => 'Nursery', 'deactivated' => 1],
                ['id' => 127, 'name' => 'Government', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 128, 'name' => 'Commerce', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 130, 'name' => 'Biology', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 131, 'name' => 'Literature', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 132, 'name' => 'Economics', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 134, 'name' => 'Accounting', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 135, 'name' => 'Marketing', 'section' => 'Senior Secondary', 'deactivated' => 0],
                ['id' => 200, 'name' => 'Mathematics', 'section' => null, 'deactivated' => 0],
                ['id' => 201, 'name' => 'English Language', 'section' => null, 'deactivated' => 0],
                ['id' => 202, 'name' => 'Agricultural Science', 'section' => null, 'deactivated' => 0],
                ['id' => 203, 'name' => 'Christian Religious Studies', 'section' => null, 'deactivated' => 0],
                ['id' => 204, 'name' => 'Civic Education', 'section' => null, 'deactivated' => 0],
                ['id' => 205, 'name' => 'Home Economics', 'section' => null, 'deactivated' => 0],
                ['id' => 206, 'name' => 'Information Technology', 'section' => null, 'deactivated' => 0],
                ['id' => 207, 'name' => 'Igbo Language', 'section' => null, 'deactivated' => 0],
                ['id' => 208, 'name' => 'Physical and Health Education', 'section' => null, 'deactivated' => 0],
                ['id' => 210, 'name' => 'Security Education', 'section' => 'Nursery/Basic/Second', 'deactivated' => 0],
                ['id' => 211, 'name' => 'Basic Science', 'section' => 'Basic/Secondary', 'deactivated' => 0],
                ['id' => 212, 'name' => 'Basic Technology', 'section' => 'Basic/Secondary', 'deactivated' => 0],
                ['id' => 213, 'name' => 'Creative and Cultural Arts', 'section' => 'Nursery/Basic', 'deactivated' => 0],
                ['id' => 214, 'name' => 'Social Studies', 'section' => 'Basic/Secondary', 'deactivated' => 0],
                ['id' => 216, 'name' => 'General Studies', 'section' => 'Nursery/Basic', 'deactivated' => 1],
                ['id' => 217, 'name' => 'Quantitative Reasoning', 'section' => 'Basic', 'deactivated' => 0],
                ['id' => 218, 'name' => 'Verbal Reasoning', 'section' => 'Basic', 'deactivated' => 0],
                ['id' => 219, 'name' => 'Poetry', 'section' => 'Nursery/Basic', 'deactivated' => 1],
                ['id' => 220, 'name' => 'History', 'section' => 'Basic/Secondary', 'deactivated' => 0],
                ['id' => 221, 'name' => 'Calligraphy', 'section' => 'Nursery/Basic', 'deactivated' => 0],
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE subjects AUTO_INCREMENT = 41');

            // Insert subjects
            foreach ($subjects as $subject) {
                DB::table('subjects')->insert([
                    'id' => $subject['id'],
                    'name' => $subject['name'],
                    'section' => $subject['section'],
                    'deactivated' => $subject['deactivated'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported subject: {$subject['name']} (ID: {$subject['id']})");
                Log::info("Imported subject: {$subject['name']} (ID: {$subject['id']})");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($subjects, 'id'));
            DB::statement("ALTER TABLE subjects AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Subjects imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing subjects: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
