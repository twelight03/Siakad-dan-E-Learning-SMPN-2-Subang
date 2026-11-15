<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $student = auth()->user()->student;
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $currentEnrollment = $student->currentEnrollment;
        $classId = $currentEnrollment?->class_id;
        $academicYearId = $currentEnrollment?->academic_year_id;

        $classSubjects = collect();
        if ($classId && $academicYearId) {
            $classSubjects = ClassSubject::with(['subject', 'teacher.user'])
                ->where('class_id', $classId)
                ->where('academic_year_id', $academicYearId)
                ->get();
        }

        $query = Attendance::where('student_id', $student->id)
            ->with(['attendanceSession.classSubject.subject', 'attendanceSession.classSubject.teacher.user']);

        if ($request->filled('class_subject_id')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->where('class_subject_id', $request->input('class_subject_id'));
            });
        }

        $allStudentAttendances = (clone $query)->get();

        $stats = [
            'total' => $allStudentAttendances->count(),
            'present' => $allStudentAttendances->where('status', 'present')->count(),
            'excused' => $allStudentAttendances->where('status', 'excused')->count(),
            'sick' => $allStudentAttendances->where('status', 'sick')->count(),
            'absent' => $allStudentAttendances->where('status', 'absent')->count(),
        ];
        $stats['percentage'] = $stats['total'] > 0
            ? round(($stats['present'] / $stats['total']) * 100, 1)
            : 0;

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $attendances = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('student.attendances.index', compact('attendances', 'stats', 'classSubjects', 'student'));
    }
}
