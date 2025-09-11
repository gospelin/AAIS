<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeachersTableSeeder extends Seeder
{
    public function run()
    {
        $teachers = [
            ['name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'status' => 'active'],
            ['name' => 'John Brown', 'email' => 'john.brown@example.com', 'status' => 'active'],
            ['name' => 'Emily Davis', 'email' => 'emily.davis@example.com', 'status' => 'active'],
            ['name' => 'Michael Wilson', 'email' => 'michael.wilson@example.com', 'status' => 'active'],
        ];

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }
    }
}
