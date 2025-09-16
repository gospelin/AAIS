<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Models\User;

class ImportUsers extends Command
{
    protected $signature = 'import:users';
    protected $description = 'Import non-admin users from Flask database and generate password hashes';

    public function handle()
    {
        try {
            // Truncate the users table to avoid conflicts
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('users')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Truncated users table');
            Log::info('Truncated users table');

            // Ensure roles exist
            $roles = ['student', 'teacher'];
            foreach ($roles as $roleName) {
                if (!Role::where('name', $roleName)->exists()) {
                    Role::create(['name' => $roleName]);
                    $this->info("Created role: {$roleName}");
                    Log::info("Created role: {$roleName}");
                }
            }

            // Flask user data (non-admin users, starting at id=7)
            $users = [
                ['id' => 7, 'username' => 'AAIS/0559/005', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 8, 'username' => 'AAIS/0559/006', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 9, 'username' => 'AAIS/0559/007', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 10, 'username' => 'AAIS/0559/008', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 11, 'username' => 'AAIS/0559/009', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 12, 'username' => 'AAIS/0559/010', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 13, 'username' => 'AAIS/0559/011', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 14, 'username' => 'AAIS/0559/012', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 15, 'username' => 'AAIS/0559/013', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 16, 'username' => 'AAIS/0559/014', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 17, 'username' => 'AAIS/0559/015', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 18, 'username' => 'AAIS/0559/016', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 19, 'username' => 'AAIS/0559/017', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 20, 'username' => 'AAIS/0559/018', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 21, 'username' => 'AAIS/0559/019', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 22, 'username' => 'AAIS/0559/020', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 23, 'username' => 'AAIS/0559/021', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 24, 'username' => 'AAIS/0559/022', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 25, 'username' => 'AAIS/0559/023', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 26, 'username' => 'AAIS/0559/024', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 27, 'username' => 'AAIS/0559/025', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 28, 'username' => 'AAIS/0559/026', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 29, 'username' => 'AAIS/0559/027', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 30, 'username' => 'AAIS/0559/028', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 31, 'username' => 'AAIS/0559/029', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 32, 'username' => 'AAIS/0559/030', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 33, 'username' => 'AAIS/0559/031', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 34, 'username' => 'AAIS/0559/032', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 35, 'username' => 'AAIS/0559/033', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 36, 'username' => 'AAIS/0559/034', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 37, 'username' => 'AAIS/0559/035', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 38, 'username' => 'AAIS/0559/036', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 39, 'username' => 'AAIS/0559/037', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 40, 'username' => 'AAIS/0559/038', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 41, 'username' => 'AAIS/0559/039', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 42, 'username' => 'AAIS/0559/040', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 43, 'username' => 'AAIS/0559/041', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 44, 'username' => 'AAIS/0559/042', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 45, 'username' => 'AAIS/0559/043', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 46, 'username' => 'AAIS/0559/044', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 48, 'username' => 'AAIS/0559/046', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 49, 'username' => 'AAIS/0559/047', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 50, 'username' => 'AAIS/0559/048', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 51, 'username' => 'AAIS/0559/049', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 52, 'username' => 'AAIS/0559/050', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 53, 'username' => 'AAIS/0559/051', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 54, 'username' => 'AAIS/0559/052', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 55, 'username' => 'AAIS/0559/053', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 56, 'username' => 'AAIS/0559/054', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 57, 'username' => 'AAIS/0559/055', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 58, 'username' => 'AAIS/0559/056', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 59, 'username' => 'AAIS/0559/057', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 60, 'username' => 'AAIS/0559/058', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 61, 'username' => 'AAIS/0559/059', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 62, 'username' => 'AAIS/0559/060', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 63, 'username' => 'AAIS/0559/061', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 64, 'username' => 'AAIS/0559/062', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 65, 'username' => 'AAIS/0559/063', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 66, 'username' => 'AAIS/0559/064', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 67, 'username' => 'AAIS/0559/065', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 68, 'username' => 'AAIS/0559/066', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 69, 'username' => 'AAIS/0559/067', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 70, 'username' => 'AAIS/0559/068', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 72, 'username' => 'AAIS/0559/070', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 73, 'username' => 'AAIS/0559/071', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 74, 'username' => 'AAIS/0559/072', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 75, 'username' => 'AAIS/0559/073', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 76, 'username' => 'AAIS/0559/074', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 78, 'username' => 'AAIS/0559/076', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 87, 'username' => 'AAIS/0559/078', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 91, 'username' => 'AAIS/0559/081', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 92, 'username' => 'AAIS/0559/082', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 93, 'username' => 'AAIS/0559/083', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 94, 'username' => 'AAIS/0559/084', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 95, 'username' => 'AAIS/0559/085', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 96, 'username' => 'AAIS/0559/086', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 98, 'username' => 'AAIS/0559/087', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 99, 'username' => 'AAIS/0559/088', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 100, 'username' => 'AAIS/0559/089', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 101, 'username' => 'AAIS/0559/090', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 102, 'username' => 'AAIS/0559/091', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 103, 'username' => 'AAIS/0559/092', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 104, 'username' => 'AAIS/0559/093', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 105, 'username' => 'AAIS/0559/094', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 106, 'username' => 'AAIS/0559/095', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 107, 'username' => 'AAIS/0559/096', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 108, 'username' => 'AAIS/0559/097', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 109, 'username' => 'AAIS/0559/098', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 110, 'username' => 'AAIS/0559/099', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 112, 'username' => 'AAIS/0559/101', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 115, 'username' => 'AAIS/0559/104', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 118, 'username' => 'AAIS/0559/107', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 119, 'username' => 'AAIS/0559/108', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 120, 'username' => 'AAIS/0559/109', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                
                ['id' => 139, 'username' => 'AAIS/0559/118', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 140, 'username' => 'AAIS/0559/119', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 141, 'username' => 'AAIS/0559/120', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 142, 'username' => 'AAIS/0559/121', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 143, 'username' => 'AAIS/0559/122', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 144, 'username' => 'AAIS/0559/123', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 145, 'username' => 'AAIS/0559/124', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                
                ['id' => 150, 'username' => 'glory.isaac', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null],
                ['id' => 151, 'username' => 'gospel.isaac', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null],
                ['id' => 152, 'username' => 'joy.onyenwe', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null],
                ['id' => 153, 'username' => 'kindness.ugorji', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null],
                ['id' => 154, 'username' => 'rose.remi', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null],
                ['id' => 155, 'username' => 'AAIS/0559/126', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 156, 'username' => 'AAIS/0559/127', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 157, 'username' => 'AAIS/0559/128', 'role' => 'student', 'active' => 1, 'mfa_secret' => null],
                ['id' => 158, 'username' => 'chidinma.chinyeaka', 'role' => 'teacher', 'active' => 1, 'mfa_secret' => null]
            ];

            $now = now()->toDateTimeString();

            // Disable auto-increment temporarily to preserve IDs
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 7');

            // Insert users with passwords and additional fields
            foreach ($users as $user) {
                $password = $user['username']; // Password is the username
                $passwordHash = Hash::make($password); // Generate bcrypt hash
                DB::table('users')->insert([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'password' => $passwordHash, // Set password immediately
                    'role' => $user['role'],
                    'active' => $user['active'],
                    'mfa_secret' => $user['mfa_secret'],
                    'name' => null,
                    'email' => null,
                    'identifier' => null,
                    'status' => $user['active'] ? 'active' : 'inactive',
                    'email_verified_at' => null,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                // Assign Spatie role
                $userModel = User::find($user['id']);
                if ($userModel) {
                    $userModel->assignRole($user['role']);
                    $this->info("Imported user: {$user['username']} (ID: {$user['id']}) with password and role: {$user['role']}");
                    Log::info("Imported user: {$user['username']} (ID: {$user['id']}) with password and role: {$user['role']}");
                } else {
                    $this->error("Failed to find user: {$user['username']} (ID: {$user['id']}) for role assignment");
                    Log::error("Failed to find user: {$user['username']} (ID: {$user['id']}) for role assignment");
                }
            }

            // Set auto-increment to max ID + 1
            $maxId = max(array_column($users, 'id'));
            DB::statement("ALTER TABLE users AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("User data imported, passwords generated, and roles assigned successfully. Auto-increment set to " . ($maxId + 1));
        } catch (\Exception $e) {
            Log::error("Error importing users: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
