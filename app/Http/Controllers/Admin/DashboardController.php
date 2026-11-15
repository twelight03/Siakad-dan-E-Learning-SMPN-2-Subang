<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_classes' => SchoolClass::count(),
            'total_subjects' => Subject::count(),
            'active_academic_year' => AcademicYear::where('is_active', true)->first(),
        ];

        // Distribution of students per class
        $classes = SchoolClass::withCount('enrollments')->get();
        $classLabels = $classes->pluck('name')->toArray();
        $classCounts = $classes->pluck('enrollments_count')->toArray();

        // Distribution of users by role
        $roles = Role::withCount('users')->get();
        $roleLabels = $roles->pluck('display_name')->toArray();
        $roleCounts = $roles->pluck('users_count')->toArray();

        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'classLabels',
            'classCounts',
            'roleLabels',
            'roleCounts'
        ));
    }
}
