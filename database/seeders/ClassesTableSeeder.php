<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    public function run()
    {
        $classes = [
            ['name' => 'Class 5', 'section' => 'A'],
            ['name' => 'Class 5', 'section' => 'B'],
            ['name' => 'Class 6', 'section' => 'A'],
            ['name' => 'Class 6', 'section' => 'B'],
            ['name' => 'Class 7', 'section' => 'A'],
            ['name' => 'Class 7', 'section' => 'B'],
            ['name' => 'Class 8', 'section' => 'A'],
            ['name' => 'Class 8', 'section' => 'B'],
            ['name' => 'Class 9', 'section' => 'A'],
            ['name' => 'Class 9', 'section' => 'B'],
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}