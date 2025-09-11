<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTeacherTableSeeder extends Seeder
{
    public function run()
    {
        $classTeachers = [
            ['class_id' => 1, 'teacher_id' => 1],
            ['class_id' => 3, 'teacher_id' => 2],
        ];

        foreach ($classTeachers as $ct) {
            DB::table('class_teacher')->insert($ct);
        }
    }
}