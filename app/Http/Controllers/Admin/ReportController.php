<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $totalClasses = SchoolClass::count();
        $totalEnrollments = $activeYear ? StudentEnrollment::where('academic_year_id', $activeYear->id)->count() : 0;
        $totalSessions = AttendanceSession::count();
        $totalGrades = Grade::count();

        return view('admin.reports.index', compact('activeYear', 'totalClasses', 'totalEnrollments', 'totalSessions', 'totalGrades'));
    }

    public function students(Request $request): View
    {
        $academicYears = AcademicYear::latest('id')->get();
        $classes = SchoolClass::orderBy('name')->get();

        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_active', true)->value('id'));
        $selectedClassId = $request->input('class_id', $classes->first()?->id);

        $selectedClass = $classes->firstWhere('id', $selectedClassId);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);

        $enrollments = collect();
        if ($selectedClassId && $selectedYearId) {
            $enrollments = StudentEnrollment::with(['student.user'])
                ->where('class_id', $selectedClassId)
                ->where('academic_year_id', $selectedYearId)
                ->get()
                ->sortBy('student.user.name');
        }

        $viewName = $request->boolean('print') ? 'admin.reports.print.students' : 'admin.reports.students';
        return view($viewName, compact('academicYears', 'classes', 'selectedYear', 'selectedClass', 'enrollments'));
    }

    public function attendances(Request $request): View
    {
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user'])
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $selectedClassSubject = null;
        $studentsRecap = collect();
        $totalSessions = 0;

        if ($request->filled('class_subject_id')) {
            $selectedClassSubject = $classSubjects->firstWhere('id', $request->input('class_subject_id'));
        } else {
            $selectedClassSubject = $classSubjects->first();
        }

        if ($selectedClassSubject) {
            $sessions = AttendanceSession::where('class_subject_id', $selectedClassSubject->id)->get();
            $totalSessions = $sessions->count();
            $sessionIds = $sessions->pluck('id');

            $enrollments = StudentEnrollment::with('student.user')
                ->where('class_id', $selectedClassSubject->class_id)
                ->where('academic_year_id', $selectedClassSubject->academic_year_id)
                ->get()
                ->sortBy('student.user.name');

            $allAttendances = Attendance::whereIn('attendance_session_id', $sessionIds)->get();

            $studentsRecap = $enrollments->map(function ($enr) use ($allAttendances, $totalSessions) {
                $studentAtt = $allAttendances->where('student_id', $enr->student_id);
                $present = $studentAtt->where('status', 'present')->count();
                $excused = $studentAtt->where('status', 'excused')->count();
                $sick = $studentAtt->where('status', 'sick')->count();
                $absent = $studentAtt->where('status', 'absent')->count();
                $percentage = $totalSessions > 0 ? round(($present / $totalSessions) * 100, 1) : 0;

                return [
                    'student' => $enr->student,
                    'present' => $present,
                    'excused' => $excused,
                    'sick' => $sick,
                    'absent' => $absent,
                    'percentage' => $percentage,
                ];
            });
        }

        $viewName = $request->boolean('print') ? 'admin.reports.print.attendances' : 'admin.reports.attendances';
        return view($viewName, compact('classSubjects', 'selectedClassSubject', 'studentsRecap', 'totalSessions'));
    }

    public function grades(Request $request): View
    {
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user', 'gradeComponents'])
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $selectedClassSubject = null;
        $gradesRecap = collect();
        $components = collect();

        if ($request->filled('class_subject_id')) {
            $selectedClassSubject = $classSubjects->firstWhere('id', $request->input('class_subject_id'));
        } else {
            $selectedClassSubject = $classSubjects->first();
        }

        if ($selectedClassSubject) {
            $components = GradeComponent::where('class_subject_id', $selectedClassSubject->id)->get();
            $enrollments = StudentEnrollment::with('student.user')
                ->where('class_id', $selectedClassSubject->class_id)
                ->where('academic_year_id', $selectedClassSubject->academic_year_id)
                ->get()
                ->sortBy('student.user.name');

            $allGrades = Grade::whereIn('grade_component_id', $components->pluck('id'))->get();

            $kkm = $selectedClassSubject->subject->kkm;

            $gradesRecap = $enrollments->map(function ($enr) use ($components, $allGrades, $kkm) {
                $compScores = [];
                $weightedSum = 0;
                $totalWeight = $components->sum('weight');

                foreach ($components as $comp) {
                    $grade = $allGrades->where('grade_component_id', $comp->id)->where('student_id', $enr->student_id)->first();
                    $score = $grade ? (float) $grade->score : null;
                    $compScores[$comp->id] = $score;
                    if ($score !== null) {
                        $weightedSum += $score * ($comp->weight / 100);
                    }
                }

                $finalScore = $totalWeight > 0 ? round(($weightedSum / ($totalWeight / 100)), 2) : 0;
                $isPassed = $finalScore >= $kkm;

                $predicate = 'D';
                if ($finalScore >= 90) {
                    $predicate = 'A';
                } elseif ($finalScore >= 80) {
                    $predicate = 'B';
                } elseif ($finalScore >= 70) {
                    $predicate = 'C';
                }

                return [
                    'student' => $enr->student,
                    'scores' => $compScores,
                    'finalScore' => $finalScore,
                    'isPassed' => $isPassed,
                    'predicate' => $predicate,
                ];
            });
        }

        $viewName = $request->boolean('print') ? 'admin.reports.print.grades' : 'admin.reports.grades';
        return view($viewName, compact('classSubjects', 'selectedClassSubject', 'components', 'gradesRecap'));
    }

    public function assignments(Request $request): View
    {
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user'])
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $selectedClassSubject = null;
        $assignments = collect();

        if ($request->filled('class_subject_id')) {
            $selectedClassSubject = $classSubjects->firstWhere('id', $request->input('class_subject_id'));
        } else {
            $selectedClassSubject = $classSubjects->first();
        }

        if ($selectedClassSubject) {
            $assignments = Assignment::with(['submissions.grade', 'creator'])
                ->where('class_subject_id', $selectedClassSubject->id)
                ->latest('deadline')
                ->get();
        }

        $viewName = $request->boolean('print') ? 'admin.reports.print.assignments' : 'admin.reports.assignments';
        return view($viewName, compact('classSubjects', 'selectedClassSubject', 'assignments'));
    }
}
