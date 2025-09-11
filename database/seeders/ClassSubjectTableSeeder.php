<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSubjectTableSeeder extends Seeder
{
    public function run()
    {
        $classSubjects = [
            ['class_id' => 1, 'subject_id' => 1],
            ['class_id' => 1, 'subject_id' => 2],
            ['class_id' => 3, 'subject_id' => 3],
        ];

        foreach ($classSubjects as $cs) {
            DB::table('class_subject')->insert($cs);
        }
    }
}