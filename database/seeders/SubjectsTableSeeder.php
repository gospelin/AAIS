<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectsTableSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH101'],
            ['name' => 'English', 'code' => 'ENG101'],
            ['name' => 'Science', 'code' => 'SCI101'],
            ['name' => 'History', 'code' => 'HIST101'],
            ['name' => 'Geography', 'code' => 'GEO101'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
