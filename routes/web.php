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
use App\Models\Classes;

Route::get('/', function () {
return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/news', function () {
    return view('news');
})->name('news');
Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');
Route::get('/programs', function () {
    return view('programs');
})->name('programs');

Route::get('/admissions', function () {
    return view('admissions');
})->name('admissions');
Route::get('/newsletter', function () {
    return view('newsletter');
})->name('newsletter');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('student/dashboard');
    })->name('student.dashboard');

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
    Route::get('/privileges/{userId}', [AdminUserController::class,'editAdminPrivileges'])->name('admin.edit_admin_privileges');
    Route::post('/privileges/{userId}', [AdminUserController::class, 'editAdminPrivileges']);
    Route::get('/delete/{userId}', [AdminUserController::class, 'deleteAdmin'])->name('admin.delete_admin');
    Route::post('/delete/{userId}', [AdminUserController::class, 'deleteAdmin']);

    // Students
    Route::resource('students', AdminStudentController::class)->only(['index', 'edit', 'update'])->names([
        'index' => 'admin.students.index',
        'edit' => 'admin.students.edit',
        'update' => 'admin.students.update',
    ]);

    Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('admin.students.destroy');
    Route::post('/students/toggle-fee-status/{student}', [AdminStudentController::class, 'toggleFeeStatus'])->name('admin.student_toggle_fee_status');
    Route::get('/students/add', [AdminStudentController::class, 'addStudent'])->name('admin.add_student');
    Route::post('/students/add', [AdminStudentController::class, 'addStudent']);
    Route::get('/students/stats', [AdminStudentController::class, 'getStats'])->name('admin.student_stats');
    Route::get('/students/bulk-upload', [AdminStudentController::class, 'bulkUpload'])->name('admin.bulk_upload_students');
    Route::post('/students/bulk-upload', [AdminStudentController::class, 'processBulkUpload']);

    Route::get('/students/reenroll/{student}', [AdminStudentController::class, 'studentReenroll'])->name('admin.student_reenroll');
    Route::post('/students/reenroll/{student}', [AdminStudentController::class, 'studentReenroll']);
    Route::get('/students/approve/{student}', [AdminStudentController::class, 'toggleApprovalStatus'])->name('admin.student_approve');
    Route::post('/students/approve/{student}', [AdminStudentController::class, 'toggleApprovalStatus'])->name('admin.student_approve');
    Route::get('/students/search/{action}', [AdminStudentController::class, 'searchStudents'])->name('admin.search_students');
    Route::post('/students/print-message', [AdminStudentController::class, 'printStudentMessage'])->name('admin.print_student_message');
    Route::get('/students/mark-as-left/{student}', [AdminStudentController::class, 'markAsLeft'])->name('admin.student_mark_as_left');
    Route::post('/students/mark-as-left/{student}', [AdminStudentController::class, 'markAsLeft'])->name('admin.student_mark_as_left');


    // Catch-all route for custom actions (must be last to avoid conflicts)
    Route::get('/students/{action}', [AdminStudentController::class, 'index'])->name('admin.students');

    // Sessions
    Route::get('/sessions', [AdminSessionController::class, 'manageSessions'])->name('admin.manage_academic_sessions');
    Route::post('/sessions', [AdminSessionController::class, 'store'])->name('admin.manage_academic_sessions.store');
    Route::get('/sessions/{id}/edit', [AdminSessionController::class, 'edit'])->name('admin.manage_academic_sessions.edit');
    Route::put('/sessions/{id}', [AdminSessionController::class, 'update'])->name('admin.manage_academic_sessions.update');
    Route::get('/sessions/{id}/delete', [AdminSessionController::class,'delete'])->name('admin.manage_academic_sessions.delete');
    Route::delete('/sessions/{id}', [AdminSessionController::class,'destroy'])->name('admin.manage_academic_sessions.destroy');
    Route::get('/set-current-session', [AdminSessionController::class,'setCurrentSessionForm'])->name('admin.set_current_session');
    Route::post('/set-current-session', [AdminSessionController::class,'setCurrentSession'])->name('admin.set_current_session.store');

    // // Classes Management (CRUD)
    // Route::resource('classes', AdminClassController::class)->names('admin.classes')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    // Route::get('/classes/{class}/delete', [AdminClassController::class, 'delete'])->name('admin.classes.delete');

    // Classes Management (CRUD)
    Route::resource('classes', AdminClassController::class)->names('admin.classes')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/classes/{class}/delete', [AdminClassController::class, 'delete'])->name('admin.classes.delete');

    // Class Selection and Student Actions
    Route::get('/classes/select/{action}', [AdminClassController::class, 'selectClass'])->name('admin.select_class');
    Route::post('/classes/select/{action}', [AdminClassController::class, 'selectClass']);
    Route::get('/classes/{className}/students/{action}', [AdminClassController::class, 'studentsByClass'])->name('admin.students_by_class');
    Route::get('/classes/{className}/students/{action}/search', [AdminClassController::class, 'searchStudentsByClass'])->name('admin.search_students_by_class'); // Changed to GET for consistency with filtering
    Route::post('/classes/{className}/promote/{studentId}/{action}', [AdminClassController::class, 'promoteStudent'])->name('admin.promote_student');
    Route::post('/classes/{className}/demote/{studentId}/{action}', [AdminClassController::class, 'demoteStudent'])->name('admin.demote_student');
    Route::post('/classes/{className}/delete-record/{studentId}/{action}', [AdminClassController::class, 'deleteStudentClassRecord'])->name('admin.delete_student_class_record');

    // Class-Specific AJAX Routes
    Route::get('/classes/{classId}/stats', [AdminClassController::class, 'getStats'])->name('admin.class_stats');
    Route::post('/classes/students/{studentId}/toggle-fee-status', [AdminClassController::class, 'toggleFeeStatus'])->name('admin.class_toggle_fee_status');
    Route::post('/classes/students/{studentId}/toggle-approval-status', [AdminClassController::class, 'toggleApprovalStatus'])->name('admin.class_toggle_approval_status');

    Route::get('/classes/{classId}/suggest-next', [AdminClassController::class, 'suggestNextClass'])->name('admin.suggest_next_class');
    Route::get('/classes/{classId}/suggest-previous', [AdminClassController::class, 'suggestPreviousClass'])->name('admin.suggest_previous_class');

    Route::post('/classes/{className}/bulk-promote/{action}', [AdminClassController::class, 'bulkPromoteStudents'])->name('admin.bulk_promote_students');
    Route::post('/classes/{className}/bulk-demote/{action}', [AdminClassController::class, 'bulkDemoteStudents'])->name('admin.bulk_demote_students');
    
    // Subjects
    Route::get('/subjects', [AdminSubjectController::class, 'manageSubjects'])->name('admin.subjects.manage');
    Route::post('/subjects', [AdminSubjectController::class, 'manageSubjects'])->name('admin.subjects.manage');
    Route::get('/subjects/edit/{subject}', [AdminSubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::post('/subjects/edit/{subject}', [AdminSubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::delete('/subjects/{subject}', [AdminSubjectController::class, 'destroy'])->name('admin.subjects.destroy');
    Route::get('/subjects/assign', [AdminSubjectController::class, 'assignSubjectToClass'])->name('admin.subjects.assign');
    Route::post('/subjects/assign', [AdminSubjectController::class, 'assignSubjectToClass'])->name('admin.subjects.assign');
    Route::post('/subjects/remove', [AdminSubjectController::class, 'removeSubjectFromClass'])->name('admin.subjects.remove');
    Route::get('/subjects/assignment/{className}', [AdminSubjectController::class, 'editSubjectAssignment'])->name('admin.subjects.edit_assignment');
    Route::post('/subjects/assignment/{className}', [AdminSubjectController::class, 'editSubjectAssignment'])->name('admin.subjects.edit_assignment');
    
    // Results
    Route::get('/results/{className}/{studentId}/{action}', [AdminResultController::class,'manageResults'])->name('admin.manage_results');
    Route::post('/results/{className}/{studentId}/{action}', [AdminResultController::class, 'updateResult']);
    Route::post('/results/update-field', [AdminResultController::class,'updateResultField'])->name('admin.update_result_field');
    Route::get('/broadsheet/{className}/{action}', [AdminResultController::class, 'broadsheet'])->name('admin.broadsheet');
    Route::post('/broadsheet/{className}/{action}', [AdminResultController::class, 'updateBroadsheet']);
    Route::get('/broadsheet/{className}/{action}/download', [AdminResultController::class,'downloadBroadsheet'])->name('admin.download_broadsheet');

    // Teachers
    Route::resource('teachers', AdminTeacherController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('/teachers', [AdminTeacherController::class, 'manageTeachers'])->name('admin.manage_teachers');
    Route::post('/teachers', [AdminTeacherController::class, 'manageTeachers']);
    Route::post('/assign-subject-to-teacher', [AdminTeacherController::class,'assignSubjectToTeacher'])->name('admin.assign_subject_to_teacher');
    Route::post('/assign-teacher-to-class', [AdminTeacherController::class,'assignTeacherToClass'])->name('admin.assign_teacher_to_class');
    Route::post('/remove-teacher-from-class', [AdminTeacherController::class,'removeTeacherFromClass'])->name('admin.remove_teacher_from_class');
});

Route::get('/admin/classes/{class}/subjects', [AdminSubjectController::class, 'getClassSubjects'])->name('admin.classes.subjects');

require __DIR__.'/auth.php';
