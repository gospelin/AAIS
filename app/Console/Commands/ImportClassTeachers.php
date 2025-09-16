<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportClassTeachers extends Command
{
    protected $signature = 'import:class-teachers';
    protected $description = 'Import class teachers from Flask database';

    public function handle()
    {
        try {
            // Truncate the class_teacher table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('class_teacher')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated class_teacher table');
            Log::info('Truncated class_teacher table');

            // Class teacher data
            $classTeachers = [
                ['class_id' => 1, 'teacher_id' => 9, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 3, 'teacher_id' => 9, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 4, 'teacher_id' => 7, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 4, 'teacher_id' => 7, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 5, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 1, 'term' => 'Third'],
                ['class_id' => 6, 'teacher_id' => 9, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 7, 'teacher_id' => 4, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 7, 'teacher_id' => 4, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 8, 'teacher_id' => 4, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 8, 'teacher_id' => 4, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 9, 'teacher_id' => 6, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 9, 'teacher_id' => 6, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 10, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 1, 'term' => 'Third'],
                ['class_id' => 10, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'First'],
                ['class_id' => 10, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 10, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 10, 'teacher_id' => 6, 'is_form_teacher' => 0, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 11, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 1, 'term' => 'Third'],
                ['class_id' => 11, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'First'],
                ['class_id' => 11, 'teacher_id' => 8, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
                ['class_id' => 12, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'First'],
                ['class_id' => 12, 'teacher_id' => 5, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 12, 'teacher_id' => 6, 'is_form_teacher' => 0, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 13, 'teacher_id' => 6, 'is_form_teacher' => 0, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 13, 'teacher_id' => 8, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Second'],
                ['class_id' => 13, 'teacher_id' => 8, 'is_form_teacher' => 1, 'session_id' => 2, 'term' => 'Third'],
            ];

            $now = now()->toDateTimeString();

            // Insert class teachers
            foreach ($classTeachers as $classTeacher) {
                DB::table('class_teacher')->insert([
                    'class_id' => $classTeacher['class_id'],
                    'teacher_id' => $classTeacher['teacher_id'],
                    'is_form_teacher' => $classTeacher['is_form_teacher'],
                    'session_id' => $classTeacher['session_id'],
                    'term' => $classTeacher['term'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported class teacher: class_id {$classTeacher['class_id']}, teacher_id {$classTeacher['teacher_id']}, session_id {$classTeacher['session_id']}, term {$classTeacher['term']}");
                Log::info("Imported class teacher: class_id {$classTeacher['class_id']}, teacher_id {$classTeacher['teacher_id']}, session_id {$classTeacher['session_id']}, term {$classTeacher['term']}");
            }

            $this->info("Class teachers imported successfully.");
        } catch (\Exception $e) {
            Log::error("Error importing class teachers: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
