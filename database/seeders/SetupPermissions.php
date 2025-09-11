<?php
// database/seeders/SetupPermissions.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupPermissions extends Seeder
{
    public function run()
    {
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
    }
}
