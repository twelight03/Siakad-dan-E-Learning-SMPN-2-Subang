<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Schedule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;

        if (! $student) {
            return view('student.dashboard', [
                'student' => null,
                'currentEnrollment' => null,
                'todaySchedules' => collect(),
                'recentMaterials' => collect(),
                'pendingAssignments' => collect(),
                'recentGrades' => collect(),
                'attendanceSummary' => ['present' => 0, 'excused' => 0, 'sick' => 0, 'absent' => 0],
            ]);
        }

        $currentEnrollment = $student->currentEnrollment()->with(['schoolClass', 'academicYear'])->first();
        $classId = $currentEnrollment?->class_id;

        $dayName = strtolower(now()->englishDayOfWeek);
        $todaySchedules = $classId
            ? Schedule::with(['classSubject.subject', 'classSubject.teacher.user'])
                ->whereHas('classSubject', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->where('day_of_week', $dayName)
                ->orderBy('start_time')
                ->get()
            : collect();

        $recentMaterials = $classId
            ? Material::with(['classSubject.subject', 'uploader'])
                ->whereHas('classSubject', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $pendingAssignments = $classId
            ? Assignment::with(['classSubject.subject'])
                ->whereHas('classSubject', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->where('deadline', '>=', now())
                ->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->orderBy('deadline')
                ->take(5)
                ->get()
            : collect();

        $recentGrades = Grade::with(['gradeComponent.classSubject.subject'])
            ->where('student_id', $student->id)
            ->latest()
            ->take(5)
            ->get();

        $attendanceCounts = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $attendanceSummary = [
            'present' => $attendanceCounts['present'] ?? 0,
            'excused' => $attendanceCounts['excused'] ?? 0,
            'sick' => $attendanceCounts['sick'] ?? 0,
            'absent' => $attendanceCounts['absent'] ?? 0,
        ];

        return view('student.dashboard', compact(
            'student',
            'currentEnrollment',
            'todaySchedules',
            'recentMaterials',
            'pendingAssignments',
            'recentGrades',
            'attendanceSummary'
        ));
    }
}
