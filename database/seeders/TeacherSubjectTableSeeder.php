<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectTableSeeder extends Seeder
{
    public function run()
    {
        $teacherSubjects = [
            ['teacher_id' => 1, 'subject_id' => 1],
            ['teacher_id' => 2, 'subject_id' => 2],
            ['teacher_id' => 3, 'subject_id' => 3],
        ];

        foreach ($teacherSubjects as $ts) {
            DB::table('teacher_subject')->insert($ts);
        }
    }
}