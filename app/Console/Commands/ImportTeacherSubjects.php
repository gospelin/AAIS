<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportTeacherSubjects extends Command
{
    protected $signature = 'import:teacher-subjects';
    protected $description = 'Import teacher subjects from Flask database';

    public function handle()
    {
        try {
            // Truncate the teacher_subject table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('teacher_subject')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated teacher_subject table');
            Log::info('Truncated teacher_subject table');

            // Teacher subject data
            $teacherSubjects = [
                ['teacher_id' => 6, 'subject_id' => 207],
            ];

            $now = now()->toDateTimeString();

            // Insert teacher subjects
            foreach ($teacherSubjects as $teacherSubject) {
                DB::table('teacher_subject')->insert([
                    'teacher_id' => $teacherSubject['teacher_id'],
                    'subject_id' => $teacherSubject['subject_id'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported teacher subject: teacher_id {$teacherSubject['teacher_id']}, subject_id {$teacherSubject['subject_id']}");
                Log::info("Imported teacher subject: teacher_id {$teacherSubject['teacher_id']}, subject_id {$teacherSubject['subject_id']}");
            }

            $this->info("Teacher subjects imported successfully.");
        } catch (\Exception $e) {
            Log::error("Error importing teacher subjects: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
