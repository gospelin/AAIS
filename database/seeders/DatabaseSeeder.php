<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ClassesTableSeeder::class,
            SubjectsTableSeeder::class,
            TeachersTableSeeder::class,
            StudentsTableSeeder::class,
            AcademicSessionsTableSeeder::class,
            ResultsTableSeeder::class,
            ClassSubjectTableSeeder::class,
            TeacherSubjectTableSeeder::class,
            ClassTeacherTableSeeder::class,
        ]);
    }
}