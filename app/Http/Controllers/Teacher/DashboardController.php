<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\Schedule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        if (! $teacher) {
            return view('teacher.dashboard', [
                'teacher' => null,
                'classSubjects' => collect(),
                'todaySchedules' => collect(),
                'activeAssignments' => collect(),
            ]);
        }

        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'academicYear'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        $dayName = strtolower(now()->englishDayOfWeek); // e.g. "monday"
        $todaySchedules = Schedule::with(['classSubject.schoolClass', 'classSubject.subject'])
            ->whereHas('classSubject', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('day_of_week', $dayName)
            ->orderBy('start_time')
            ->get();

        $activeAssignments = Assignment::with(['classSubject.schoolClass', 'classSubject.subject', 'submissions'])
            ->whereHas('classSubject', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('deadline', '>=', now())
            ->latest('deadline')
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('teacher', 'classSubjects', 'todaySchedules', 'activeAssignments'));
    }
}
