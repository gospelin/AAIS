<?php

namespace Database\Seeders;

use App\Models\Result;
use Illuminate\Database\Seeder;

class ResultsTableSeeder extends Seeder
{
    public function run()
    {
        $results = [
            ['student_id' => 1, 'subject_id' => 1, 'score' => 85.5],
            ['student_id' => 1, 'subject_id' => 2, 'score' => 90.0],
            ['student_id' => 2, 'subject_id' => 1, 'score' => 78.0],
            ['student_id' => 3, 'subject_id' => 3, 'score' => 92.5],
        ];

        foreach ($results as $result) {
            Result::create($result);
        }
    }
}