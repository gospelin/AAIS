<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportStudentClassHistory extends Command
{
    protected $signature = 'import:student-class-history';
    protected $description = 'Import student class history from Flask database';

    public function handle()
    {
        try {
            // Truncate the student_class_history table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('student_class_history')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated student_class_history table');
            Log::info('Truncated student_class_history table');

            // Student class history data
            $studentClassHistory = [
                ['id' => 1, 'student_id' => 11, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 4, 'student_id' => 7, 'session_id' => 1, 'class_id' => 2, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 5, 'student_id' => 8, 'session_id' => 1, 'class_id' => 2, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 6, 'student_id' => 9, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 7, 'student_id' => 10, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 8, 'student_id' => 12, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 9, 'student_id' => 13, 'session_id' => 1, 'class_id' => 2, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 10, 'student_id' => 14, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 11, 'student_id' => 15, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 12, 'student_id' => 16, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 13, 'student_id' => 17, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 14, 'student_id' => 18, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 15, 'student_id' => 19, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 17, 'student_id' => 78, 'session_id' => 1, 'class_id' => 5, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 18, 'student_id' => 21, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 19, 'student_id' => 22, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 20, 'student_id' => 23, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 21, 'student_id' => 24, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 22, 'student_id' => 25, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 23, 'student_id' => 26, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 24, 'student_id' => 27, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 25, 'student_id' => 28, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 26, 'student_id' => 29, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 27, 'student_id' => 30, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 28, 'student_id' => 31, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 29, 'student_id' => 32, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 30, 'student_id' => 33, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 31, 'student_id' => 34, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 32, 'student_id' => 35, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 33, 'student_id' => 36, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 34, 'student_id' => 37, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 35, 'student_id' => 38, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 36, 'student_id' => 39, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 37, 'student_id' => 40, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 38, 'student_id' => 41, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 39, 'student_id' => 42, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 40, 'student_id' => 43, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 41, 'student_id' => 44, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 42, 'student_id' => 46, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 43, 'student_id' => 47, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 44, 'student_id' => 48, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 45, 'student_id' => 49, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 46, 'student_id' => 50, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 47, 'student_id' => 51, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 48, 'student_id' => 52, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 49, 'student_id' => 53, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 50, 'student_id' => 54, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 51, 'student_id' => 55, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 52, 'student_id' => 56, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 53, 'student_id' => 57, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 54, 'student_id' => 58, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 56, 'student_id' => 60, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 61, 'student_id' => 66, 'session_id' => 1, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 62, 'student_id' => 67, 'session_id' => 1, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 64, 'student_id' => 70, 'session_id' => 1, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 65, 'student_id' => 71, 'session_id' => 1, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 66, 'student_id' => 72, 'session_id' => 1, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 67, 'student_id' => 73, 'session_id' => 1, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 68, 'student_id' => 74, 'session_id' => 1, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 69, 'student_id' => 76, 'session_id' => 1, 'class_id' => 5, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 70, 'student_id' => 81, 'session_id' => 1, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 71, 'student_id' => 65, 'session_id' => 1, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 72, 'student_id' => 82, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 73, 'student_id' => 83, 'session_id' => 1, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 74, 'student_id' => 84, 'session_id' => 1, 'class_id' => 5, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 75, 'student_id' => 85, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 76, 'student_id' => 86, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 78, 'student_id' => 74, 'session_id' => 2, 'class_id' => 12, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 79, 'student_id' => 65, 'session_id' => 2, 'class_id' => 12, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 81, 'student_id' => 73, 'session_id' => 2, 'class_id' => 10, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 82, 'student_id' => 55, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 83, 'student_id' => 56, 'session_id' => 1, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 87, 'student_id' => 61, 'session_id' => 1, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 88, 'student_id' => 63, 'session_id' => 1, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 89, 'student_id' => 62, 'session_id' => 1, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 90, 'student_id' => 61, 'session_id' => 2, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 93, 'student_id' => 48, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 94, 'student_id' => 49, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 95, 'student_id' => 7, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 96, 'student_id' => 11, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 97, 'student_id' => 9, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 98, 'student_id' => 12, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 99, 'student_id' => 15, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 100, 'student_id' => 16, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 101, 'student_id' => 17, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 102, 'student_id' => 19, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 104, 'student_id' => 21, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 105, 'student_id' => 22, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 106, 'student_id' => 23, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 107, 'student_id' => 24, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 108, 'student_id' => 25, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 111, 'student_id' => 28, 'session_id' => 2, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 112, 'student_id' => 29, 'session_id' => 2, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 113, 'student_id' => 30, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 114, 'student_id' => 31, 'session_id' => 2, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 115, 'student_id' => 32, 'session_id' => 1, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 116, 'student_id' => 33, 'session_id' => 2, 'class_id' => 3, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 117, 'student_id' => 35, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 118, 'student_id' => 36, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 120, 'student_id' => 38, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 121, 'student_id' => 39, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 122, 'student_id' => 40, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 123, 'student_id' => 42, 'session_id' => 2, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 125, 'student_id' => 44, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 126, 'student_id' => 46, 'session_id' => 2, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 127, 'student_id' => 51, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 128, 'student_id' => 53, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 130, 'student_id' => 85, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 131, 'student_id' => 58, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 133, 'student_id' => 59, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 135, 'student_id' => 59, 'session_id' => 1, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 137, 'student_id' => 64, 'session_id' => 1, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 138, 'student_id' => 64, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 142, 'student_id' => 54, 'session_id' => 2, 'class_id' => 9, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 143, 'student_id' => 87, 'session_id' => 2, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 144, 'student_id' => 88, 'session_id' => 1, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 146, 'student_id' => 90, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 147, 'student_id' => 91, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 148, 'student_id' => 92, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 149, 'student_id' => 93, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 150, 'student_id' => 94, 'session_id' => 1, 'class_id' => 6, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 152, 'student_id' => 96, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 155, 'student_id' => 99, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 158, 'student_id' => 57, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 169, 'student_id' => 84, 'session_id' => 2, 'class_id' => 14, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 183, 'student_id' => 83, 'session_id' => 2, 'class_id' => 8, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 184, 'student_id' => 60, 'session_id' => 2, 'class_id' => 13, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 193, 'student_id' => 107, 'session_id' => 2, 'class_id' => 2, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 196, 'student_id' => 108, 'session_id' => 2, 'class_id' => 2, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 201, 'student_id' => 104, 'session_id' => 2, 'class_id' => 2, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-02 15:49:46', 'leave_date' => null, 'is_active' => 1],
                ['id' => 203, 'student_id' => 62, 'session_id' => 2, 'class_id' => 11, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-02 19:09:39', 'leave_date' => '2025-03-08 12:23:03', 'is_active' => 1],
                ['id' => 207, 'student_id' => 95, 'session_id' => 1, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-03 13:22:16', 'leave_date' => null, 'is_active' => 1],
                ['id' => 208, 'student_id' => 89, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-03 13:22:47', 'leave_date' => null, 'is_active' => 1],
                ['id' => 210, 'student_id' => 118, 'session_id' => 2, 'class_id' => 2, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:47:10', 'leave_date' => null, 'is_active' => 1],
                ['id' => 211, 'student_id' => 119, 'session_id' => 2, 'class_id' => 2, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:47:47', 'leave_date' => null, 'is_active' => 1],
                ['id' => 212, 'student_id' => 120, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:48:45', 'leave_date' => null, 'is_active' => 1],
                ['id' => 213, 'student_id' => 121, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:49:21', 'leave_date' => null, 'is_active' => 1],
                ['id' => 214, 'student_id' => 122, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:52:28', 'leave_date' => null, 'is_active' => 1],
                ['id' => 215, 'student_id' => 123, 'session_id' => 2, 'class_id' => 4, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:52:59', 'leave_date' => null, 'is_active' => 1],
                ['id' => 216, 'student_id' => 124, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-03 14:54:26', 'leave_date' => null, 'is_active' => 1],
                ['id' => 225, 'student_id' => 68, 'session_id' => 1, 'class_id' => 11, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2023-09-01 02:00:00', 'leave_date' => null, 'is_active' => 1],
                ['id' => 227, 'student_id' => 68, 'session_id' => 2, 'class_id' => 10, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-01-01 02:00:00', 'leave_date' => null, 'is_active' => 1],
                ['id' => 228, 'student_id' => 72, 'session_id' => 2, 'class_id' => 10, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-06 18:45:34', 'leave_date' => '2025-03-08 12:24:05', 'is_active' => 1],
                ['id' => 229, 'student_id' => 63, 'session_id' => 2, 'class_id' => 11, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-06 18:46:24', 'leave_date' => '2025-03-08 12:22:42', 'is_active' => 1],
                ['id' => 230, 'student_id' => 27, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-08 10:13:50', 'leave_date' => '2025-03-08 10:33:30', 'is_active' => 1],
                ['id' => 231, 'student_id' => 43, 'session_id' => 2, 'class_id' => 8, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-06 18:46:47', 'leave_date' => '2025-03-08 12:17:40', 'is_active' => 1],
                ['id' => 232, 'student_id' => 20, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-06 18:47:29', 'leave_date' => '2025-03-07 21:43:36', 'is_active' => 0],
                ['id' => 233, 'student_id' => 37, 'session_id' => 2, 'class_id' => 7, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-06 18:47:37', 'leave_date' => '2025-03-08 12:15:44', 'is_active' => 1],
                ['id' => 235, 'student_id' => 26, 'session_id' => 2, 'class_id' => 6, 'start_term' => 'First', 'end_term' => 'Second', 'join_date' => '2025-03-07 10:12:44', 'leave_date' => '2025-03-08 12:14:30', 'is_active' => 1],
                ['id' => 237, 'student_id' => 20, 'session_id' => 1, 'class_id' => 4, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-07 10:19:57', 'leave_date' => null, 'is_active' => 1],
                ['id' => 238, 'student_id' => 95, 'session_id' => 2, 'class_id' => 1, 'start_term' => 'First', 'end_term' => null, 'join_date' => '2025-03-17 23:27:14', 'leave_date' => null, 'is_active' => 1],
                ['id' => 242, 'student_id' => 126, 'session_id' => 2, 'class_id' => 3, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-25 07:26:04', 'leave_date' => null, 'is_active' => 1],
                ['id' => 243, 'student_id' => 127, 'session_id' => 2, 'class_id' => 10, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-26 16:25:02', 'leave_date' => null, 'is_active' => 1],
                ['id' => 244, 'student_id' => 128, 'session_id' => 2, 'class_id' => 10, 'start_term' => 'Second', 'end_term' => null, 'join_date' => '2025-03-26 16:37:07', 'leave_date' => null, 'is_active' => 1]
            ];

            // Set auto-increment starting value
            DB::statement('ALTER TABLE student_class_history AUTO_INCREMENT = 1');

            // Insert data
            foreach ($studentClassHistory as $record) {
                DB::table('student_class_history')->insert([
                    'id' => $record['id'],
                    'student_id' => $record['student_id'],
                    'session_id' => $record['session_id'],
                    'class_id' => $record['class_id'],
                    'start_term' => $record['start_term'],
                    'end_term' => $record['end_term'],
                    'join_date' => $record['join_date'],
                    'leave_date' => $record['leave_date'],
                    'is_active' => $record['is_active'],
                    'created_at' => $record['join_date'],
                    'updated_at' => $record['join_date']
                ]);
                $this->info("Imported student_class_history: ID {$record['id']} for student_id {$record['student_id']}");
                Log::info("Imported student_class_history: ID {$record['id']} for student_id {$record['student_id']}");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($studentClassHistory, 'id'));
            DB::statement("ALTER TABLE student_class_history AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Student class history data imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing student class history: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
