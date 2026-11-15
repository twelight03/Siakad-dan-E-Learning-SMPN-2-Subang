<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'total_students' => 0,
        'total_teachers' => 0,
        'total_classes' => 0,
        'total_subjects' => 0,
        'active_year' => null,
    ];

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
            $stats['total_students'] = \App\Models\Student::count();
            $stats['total_teachers'] = \App\Models\Teacher::count();
            $stats['total_classes'] = \App\Models\SchoolClass::count();
            $stats['total_subjects'] = \App\Models\Subject::count();
            $stats['active_year'] = \App\Models\AcademicYear::where('is_active', true)->first();
        }
    } catch (\Throwable $e) {
        // Fallback for tests or fresh installations
    }

    return view('welcome', compact('stats'));
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isTeacher()) {
        return redirect()->route('teacher.dashboard');
    }
    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    }
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    Route::resource('classes', \App\Http\Controllers\Admin\SchoolClassController::class);
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::resource('enrollments', \App\Http\Controllers\Admin\StudentEnrollmentController::class);
    Route::resource('class-subjects', \App\Http\Controllers\Admin\ClassSubjectController::class);
    Route::get('schedules/settings', [\App\Http\Controllers\Admin\ScheduleController::class, 'settings'])->name('schedules.settings');
    Route::post('schedules/settings', [\App\Http\Controllers\Admin\ScheduleController::class, 'updateSettings'])->name('schedules.settings.update');
    Route::get('schedules/auto-generate', [\App\Http\Controllers\Admin\ScheduleGeneratorController::class, 'index'])->name('schedules.auto-generate.index');
    Route::post('schedules/auto-generate', [\App\Http\Controllers\Admin\ScheduleGeneratorController::class, 'generate'])->name('schedules.auto-generate.run');
    Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class);
    Route::get('promotions', [\App\Http\Controllers\Admin\ClassPromotionController::class, 'index'])->name('promotions.index');
    Route::post('promotions/direct', [\App\Http\Controllers\Admin\ClassPromotionController::class, 'directPromotion'])->name('promotions.direct');
    Route::post('promotions/distribute/preview', [\App\Http\Controllers\Admin\ClassPromotionController::class, 'previewDistribution'])->name('promotions.distribute.preview');
    Route::post('promotions/distribute/apply', [\App\Http\Controllers\Admin\ClassPromotionController::class, 'applyDistribution'])->name('promotions.distribute.apply');
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/students', [\App\Http\Controllers\Admin\ReportController::class, 'students'])->name('reports.students');
    Route::get('reports/attendances', [\App\Http\Controllers\Admin\ReportController::class, 'attendances'])->name('reports.attendances');
    Route::get('reports/grades', [\App\Http\Controllers\Admin\ReportController::class, 'grades'])->name('reports.grades');
    Route::get('reports/assignments', [\App\Http\Controllers\Admin\ReportController::class, 'assignments'])->name('reports.assignments');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::resource('attendances', \App\Http\Controllers\Teacher\AttendanceController::class);
    Route::resource('grade-components', \App\Http\Controllers\Teacher\GradeComponentController::class);
    Route::get('grade-components/{grade_component}/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('grades.index');
    Route::post('grade-components/{grade_component}/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('grades.store');
    Route::resource('materials', \App\Http\Controllers\Teacher\MaterialController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('materials/{material}/download', [\App\Http\Controllers\Teacher\MaterialController::class, 'download'])->name('materials.download');
    Route::resource('assignments', \App\Http\Controllers\Teacher\AssignmentController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('assignments/{assignment}/attachment', [\App\Http\Controllers\Teacher\AssignmentController::class, 'downloadAttachment'])->name('assignments.attachment.download');
    Route::get('submissions/{submission}/download', [\App\Http\Controllers\Teacher\AssignmentController::class, 'downloadSubmission'])->name('assignments.submissions.download');
    Route::post('submissions/{submission}/grade', [\App\Http\Controllers\Teacher\AssignmentController::class, 'gradeSubmission'])->name('assignments.submissions.grade');
    Route::get('group-generator', [\App\Http\Controllers\Teacher\GroupGeneratorController::class, 'index'])->name('groups.index');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('schedules', [\App\Http\Controllers\Student\ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('attendances', [\App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('grades', [\App\Http\Controllers\Student\GradeController::class, 'index'])->name('grades.index');
    Route::get('materials', [\App\Http\Controllers\Student\MaterialController::class, 'index'])->name('materials.index');
    Route::get('materials/{material}/download', [\App\Http\Controllers\Student\MaterialController::class, 'download'])->name('materials.download');
    Route::get('assignments', [\App\Http\Controllers\Student\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [\App\Http\Controllers\Student\AssignmentController::class, 'show'])->name('assignments.show');
    Route::post('assignments/{assignment}/submit', [\App\Http\Controllers\Student\AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('assignments/{assignment}/attachment', [\App\Http\Controllers\Student\AssignmentController::class, 'downloadAttachment'])->name('assignments.attachment.download');
    Route::get('submissions/{submission}/download', [\App\Http\Controllers\Student\AssignmentController::class, 'downloadMySubmission'])->name('assignments.submissions.download');
});

require __DIR__.'/auth.php';
