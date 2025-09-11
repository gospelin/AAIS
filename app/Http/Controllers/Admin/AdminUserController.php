<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AdminUserController extends AdminBaseController
{
    private $google2fa;

    public function __construct()
    {
        $this->google2fa = app(Google2FA::class);
    }

    public function dashboard()
    {
        $stats = $this->getDashboardData();

        $this->logActivity('Dashboard loaded', ['stats' => array_keys($stats)]);

        return view('admin.dashboard', $stats);
    }

    protected function getDashboardData()
    {
        $currentSession = AcademicSession::getCurrentSession();
        $currentTerm = AcademicSession::getCurrentSessionAndTerm(true)[1] ?? null;

        $totalStudents = Student::whereHas('classHistory', function ($query) use ($currentSession) {
            $query->where('session_id', $currentSession?->id)
                ->where('is_active', true)
                ->whereNull('leave_date');
        })->count();

        $totalClasses = Classes::count();
        $totalSubjects = Subject::where('deactivated', false)->count();
        $totalTeachers = Teacher::count(); // Assumes Teacher model exists

        $activeAcademicSession = $currentSession ? (object) [
            'name' => $currentSession->year,
            'status' => $currentSession->is_current ? 'active' : 'inactive'
        ] : (object) ['name' => 'No Active Session', 'status' => 'inactive'];

        $recentActivities = AuditLog::with('user')
            ->where('user_id', '!=', Auth::id())
            ->latest('timestamp')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'icon' => $this->getActivityIcon($log->action),
                    'title' => $this->getActivityTitle($log->action),
                    'description' => $log->action,
                    'time' => $log->timestamp->diffForHumans(),
                ];
            });

        return compact(
            'totalStudents',
            'totalClasses',
            'totalSubjects',
            'totalTeachers',
            'activeAcademicSession',
            'recentActivities'
        );
    }

    private function getActivityIcon($action)
    {
        $icons = [
            'Created admin' => 'fas fa-user-plus',
            'Deleted student' => 'fas fa-user-times',
            'Student added' => 'fas fa-graduation-cap',
            'Result updated' => 'fas fa-chart-line',
        ];

        return $icons[$action] ?? 'fas fa-info-circle';
    }

    private function getActivityTitle($action)
    {
        $titles = [
            'Created admin' => 'New Admin Created',
            'Deleted student' => 'Student Removed',
            'Student added' => 'New Student Enrolled',
            'Result updated' => 'Results Updated',
        ];

        return $titles[$action] ?? 'Activity Log';
    }

    public function createAdmin(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'email' => 'required|email|unique:users,email',
                    'username' => 'required|string|unique:users,username|min:3|max:50',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'admin',
                    'active' => true,
                ]);

                $user->assignRole('admin');

                $secret = $this->google2fa->generateSecretKey();
                $user->update(['google2fa_secret' => $secret]);

                $this->logActivity("Created admin: {$user->username}", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                $qrCodeSvg = $this->generateQRCode($user, $secret);

                return back()->with([
                    'success' => 'Admin created successfully! Please save the QR code for 2FA setup.',
                    'qrCodeSvg' => $qrCodeSvg,
                    'secret' => $secret,
                ]);

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error creating admin: " . $e->getMessage());
                return back()->with('error', 'Database error occurred. Please try again.');
            }
        }

        return view('admin.create_admin');
    }

    public function viewAdmins()
    {
        $admins = User::role('admin')->get();

        $this->logActivity('Viewed admin list');

        return view('admin.view_admins', compact('admins'));
    }

    public function editAdmin(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'first_name' => 'required|string|max:50',
                    'middle_name' => 'nullable|string|max:50',
                    'last_name' => 'required|string|max:50',
                    'email' => 'required|email|unique:users,email,' . $userId,
                    'username' => 'required|string|unique:users,username,' . $userId . '|min:3|max:50',
                    'password' => 'nullable|string|min:8|confirmed',
                    'active' => 'required|boolean',
                ]);

                $updateData = [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'active' => $validated['active'],
                ];

                if ($validated['password']) {
                    $updateData['password'] = Hash::make($validated['password']);
                }

                $user->update($updateData);

                $this->logActivity("Updated admin: {$user->username}", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                return back()->with('success', 'Admin updated successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            } catch (\Exception $e) {
                Log::error("Database error updating admin {$userId}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.edit_admin', compact('user'));
    }

    public function editAdminPrivileges(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $roles = Role::all();
        $permissions = Permission::all();

        if ($request->isMethod('post')) {
            try {
                $validated = $request->validate([
                    'roles' => 'nullable|array',
                    'roles.*' => 'exists:roles,name',
                    'permissions' => 'nullable|array',
                    'permissions.*' => 'exists:permissions,name',
                ]);

                $user->syncRoles($validated['roles'] ?? []);
                $user->syncPermissions($validated['permissions'] ?? []);

                $this->logActivity("Updated privileges for admin: {$user->username}", [
                    'user_id' => $user->id,
                    'roles' => $validated['roles'] ?? [],
                    'permissions' => $validated['permissions'] ?? []
                ]);

                return back()->with('success', 'Admin privileges updated successfully!');

            } catch (ValidationException $e) {
                return back()->withErrors($e->errors());
            } catch (\Exception $e) {
                Log::error("Database error updating admin privileges {$userId}: " . $e->getMessage());
                return back()->with('error', 'Database error occurred.');
            }
        }

        return view('admin.edit_admin_privileges', compact('user', 'roles', 'permissions'));
    }

    public function deleteAdmin(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $username = $user->username;
            $user->delete();

            $this->logActivity("Deleted admin: {$username}", ['user_id' => $userId]);

            return back()->with('success', 'Admin deleted successfully!');

        } catch (\Exception $e) {
            Log::error("Database error deleting admin {$userId}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred.');
        }
    }

    protected function generateQRCode($user, $secret)
    {
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return base64_encode($qrCodeSvg);
    }
}
