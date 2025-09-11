<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminResultController;
use App\Http\Controllers\Admin\AdminTeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:admin', 'mfa'])->group(function () {
    // Dashboard & Users
    Route::get('/dashboard', [AdminUserController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/create', [AdminUserController::class, 'createAdmin'])->name('admin.create_admin');
    Route::post('/create', [AdminUserController::class, 'createAdmin']);
    Route::get('/view', [AdminUserController::class, 'viewAdmins'])->name('admin.view_admins');
    Route::get('/edit/{userId}', [AdminUserController::class, 'editAdmin'])->name('admin.edit_admin');
    Route::post('/edit/{userId}', [AdminUserController::class, 'editAdmin']);
    Route::get('/privileges/{userId}', [AdminUserController::class, 'editAdminPrivileges'])->name('admin.edit_admin_privileges');
    Route::post('/privileges/{userId}', [AdminUserController::class, 'editAdminPrivileges']);
    Route::get('/delete/{userId}', [AdminUserController::class, 'deleteAdmin'])->name('admin.delete_admin');
    Route::post('/delete/{userId}', [AdminUserController::class, 'deleteAdmin']);

    // Students
    Route::get('/students/add', [AdminStudentController::class, 'addStudent'])->name('admin.add_student');
    Route::post('/students/add', [AdminStudentController::class, 'addStudent']);
    Route::resource('students', AdminStudentController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('/students/{action}', [AdminStudentController::class, 'index'])->name('admin.students');
    Route::post('/students/{action}', [AdminStudentController::class, 'index']);
    Route::post('/students/{studentId}/approve', [AdminStudentController::class, 'approveStudent'])->name('admin.approve_student');
    Route::post('/students/{studentId}/reenroll', [AdminStudentController::class, 'studentReenroll'])->name('admin.student_reenroll');
    Route::post('/students/{studentId}/toggle-fee', [AdminStudentController::class, 'toggleFeeStatus'])->name('admin.toggle_fee_status');
    Route::post('/students/{studentId}/toggle-approval', [AdminStudentController::class, 'toggleApprovalStatus'])->name('admin.toggle_approval_status');
    Route::get('/students/search/{action}', [AdminStudentController::class, 'searchStudents'])->name('admin.search_students');
    Route::post('/students/print-message', [AdminStudentController::class, 'printStudentMessage'])->name('admin.print_student_message');

    // Sessions
    Route::get('/sessions', [AdminSessionController::class, 'manageSessions'])->name('admin.manage_academic_sessions');
    Route::post('/sessions', [AdminSessionController::class, 'manageSessions']);
    Route::get('sessions', [AdminSessionController::class, 'manageSessions'])->name('admin.manage_academic_sessions');
    Route::post('sessions', [AdminSessionController::class, 'store'])->name('admin.manage_academic_sessions.store');
    Route::get('sessions/{id}/edit', [AdminSessionController::class, 'edit'])->name('admin.manage_academic_sessions.edit');
    Route::put('sessions/{id}', [AdminSessionController::class, 'update'])->name('admin.manage_academic_sessions.update');
    Route::get('sessions/{id}/delete', [AdminSessionController::class, 'delete'])->name('admin.manage_academic_sessions.delete');
    Route::delete('sessions/{id}', [AdminSessionController::class, 'destroy'])->name('admin.manage_academic_sessions.destroy');
    Route::get('/classes/{class}/delete', [AdminClassController::class, 'delete'])->name('admin.classes.delete');

    // Classes Management (CRUD)
    Route::resource('classes', AdminClassController::class)->names('admin.classes')->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Class Selection and Student Actions (existing, unchanged)
    Route::get('/classes/select/{action}', [AdminClassController::class, 'selectClass'])->name('admin.select_class');
    Route::post('/classes/select/{action}', [AdminClassController::class, 'selectClass']);
    Route::get('/classes/{className}/students/{action}', [AdminClassController::class, 'studentsByClass'])->name('admin.students_by_class');
    Route::post('/classes/{className}/students/{action}/search', [AdminClassController::class, 'searchStudentsByClass'])->name('admin.search_students_by_class');
    Route::post('/classes/{className}/promote/{studentId}/{action}', [AdminClassController::class, 'promoteStudent'])->name('admin.promote_student');
    Route::post('/classes/{className}/demote/{studentId}/{action}', [AdminClassController::class, 'demoteStudent'])->name('admin.demote_student');
    Route::post('/classes/{className}/delete-record/{studentId}/{action}', [AdminClassController::class, 'deleteStudentClassRecord'])->name('admin.delete_student_class_record');

    // Subjects
    Route::resource('subjects', AdminSubjectController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/subjects', [AdminSubjectController::class, 'manageSubjects'])->name('admin.manage_subjects');
    Route::post('/subjects', [AdminSubjectController::class, 'manageSubjects']);
    Route::post('/subjects/merge', [AdminSubjectController::class, 'mergeSubjects'])->name('admin.merge_subjects');
    Route::post('/assign-subject-to-class', [AdminSubjectController::class, 'assignSubjectToClass'])->name('admin.assign_subject_to_class');
    Route::post('/remove-subject-from-class', [AdminSubjectController::class, 'removeSubjectFromClass'])->name('admin.remove_subject_from_class');
    Route::get('/classes/{className}/edit-subjects', [AdminSubjectController::class, 'editSubjectAssignment'])->name('admin.edit_subject_assignment');
    Route::post('/classes/{className}/edit-subjects', [AdminSubjectController::class, 'editSubjectAssignment']);

    // Results
    Route::get('/results/{className}/{studentId}/{action}', [AdminResultController::class, 'manageResults'])->name('admin.manage_results');
    Route::post('/results/{className}/{studentId}/{action}', [AdminResultController::class, 'updateResult']);
    Route::post('/results/update-field', [AdminResultController::class, 'updateResultField'])->name('admin.update_result_field');
    Route::get('/broadsheet/{className}/{action}', [AdminResultController::class, 'broadsheet'])->name('admin.broadsheet');
    Route::post('/broadsheet/{className}/{action}', [AdminResultController::class, 'updateBroadsheet']);
    Route::get('/broadsheet/{className}/{action}/download', [AdminResultController::class, 'downloadBroadsheet'])->name('admin.download_broadsheet');

    // Teachers
    Route::resource('teachers', AdminTeacherController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/teachers', [AdminTeacherController::class, 'manageTeachers'])->name('admin.manage_teachers');
    Route::post('/teachers', [AdminTeacherController::class, 'manageTeachers']);
    Route::post('/assign-subject-to-teacher', [AdminTeacherController::class, 'assignSubjectToTeacher'])->name('admin.assign_subject_to_teacher');
    Route::post('/assign-teacher-to-class', [AdminTeacherController::class, 'assignTeacherToClass'])->name('admin.assign_teacher_to_class');
    Route::post('/remove-teacher-from-class', [AdminTeacherController::class, 'removeTeacherFromClass'])->name('admin.remove_teacher_from_class');
});

require __DIR__.'/auth.php';
