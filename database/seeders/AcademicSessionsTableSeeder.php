<?php

namespace Database\Seeders;

use App\Models\Academic_Session;
use Illuminate\Database\Seeder;

class AcademicSessionsTableSeeder extends Seeder
{
    public function run()
    {
        Academic_Session::create([
            'name' => '2025/2026 Academic_Session',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
    }
}
