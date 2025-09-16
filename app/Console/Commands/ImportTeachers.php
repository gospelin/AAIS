<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportTeachers extends Command
{
    protected $signature = 'import:teachers';
    protected $description = 'Import teachers from Flask database';

    public function handle()
    {
        try {
            // Truncate the teacher table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('teachers')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated teachers table');
            Log::info('Truncated teachers table');

            // Teacher data
            $teachers = [
                [
                    'id' => 4,
                    'first_name' => 'Glory',
                    'last_name' => 'Isaac',
                    'phone_number' => '08161168409',
                    'user_id' => 150,
                    'employee_id' => 'glory.isaac',
                    'section' => 'Primary'
                ],
                [
                    'id' => 5,
                    'first_name' => 'Gospel',
                    'last_name' => 'Isaac',
                    'phone_number' => '08166660273',
                    'user_id' => 151,
                    'employee_id' => 'gospel.isaac',
                    'section' => 'Secondary'
                ],
                [
                    'id' => 6,
                    'first_name' => 'Joy',
                    'last_name' => 'Onyenwe',
                    'phone_number' => '09038680024',
                    'user_id' => 152,
                    'employee_id' => 'joy.onyenwe',
                    'section' => 'Primary'
                ],
                [
                    'id' => 7,
                    'first_name' => 'Kindness',
                    'last_name' => 'Ugorji',
                    'phone_number' => '08165877873',
                    'user_id' => 153,
                    'employee_id' => 'kindness.ugorji',
                    'section' => 'Nursery'
                ],
                [
                    'id' => 8,
                    'first_name' => 'Rose',
                    'last_name' => 'Remi',
                    'phone_number' => '08148136710',
                    'user_id' => 154,
                    'employee_id' => 'rose.remi',
                    'section' => 'Primary'
                ],
                [
                    'id' => 9,
                    'first_name' => 'Chidinma',
                    'last_name' => 'Chinyeaka',
                    'phone_number' => '09139048065',
                    'user_id' => 158,
                    'employee_id' => 'chidinma.chinyeaka',
                    'section' => 'Nursery'
                ],
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE teachers AUTO_INCREMENT = 4');

            // Insert teachers
            foreach ($teachers as $teacher) {
                DB::table('teachers')->insert([
                    'id' => $teacher['id'],
                    'first_name' => $teacher['first_name'],
                    'last_name' => $teacher['last_name'],
                    'phone_number' => $teacher['phone_number'],
                    'user_id' => $teacher['user_id'],
                    'employee_id' => $teacher['employee_id'],
                    'section' => $teacher['section'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("Imported teacher: {$teacher['first_name']} {$teacher['last_name']} (ID: {$teacher['id']})");
                Log::info("Imported teacher: {$teacher['first_name']} {$teacher['last_name']} (ID: {$teacher['id']})");
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($teachers, 'id'));
            DB::statement("ALTER TABLE teachers AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("Teachers imported successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing teachers: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
