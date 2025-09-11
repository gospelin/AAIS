<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        $students = [
            ['name' => 'John Doe', 'email' => 'john.doe@example.com', 'admission_number' => 'STU001', 'class_id' => 1, 'status' => 'active', 'fees_status' => 'paid'],
            ['name' => 'Alice Johnson', 'email' => 'alice.johnson@example.com', 'admission_number' => 'STU002', 'class_id' => 1, 'status' => 'active', 'fees_status' => 'unpaid'],
            ['name' => 'Bob Smith', 'email' => 'bob.smith@example.com', 'admission_number' => 'STU003', 'class_id' => 3, 'status' => 'active', 'fees_status' => 'paid'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}