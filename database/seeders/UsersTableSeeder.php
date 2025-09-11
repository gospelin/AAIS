<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role::create(['name' => 'admin']);
        // Role::create(['name' => 'staff']);
        // Role::create(['name' => 'student']);

        // Create permissions
        $permissions = [
            'manage_users',
            'manage_sessions',
            'manage_classes',
            'manage_results',
            'manage_teachers',
            'manage_subjects',
            'view_reports'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo($permissions);

        $teacher = Role::create(['name' => 'teacher']);
        $teacher->givePermissionTo(['view_reports']);

        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo(['view_reports']);


        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('Tripled@121'),
            'mfa_secret' => (new \PragmaRX\Google2FA\Google2FA())->generateSecretKey(),
            'status' => 'active',
            'email_verified_at' => now(),
            'notifications' => true,
        ])->assignRole('admin');

    }
}
