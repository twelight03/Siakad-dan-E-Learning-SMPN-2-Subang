<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentEnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);

        $query = StudentEnrollment::with(['student.user', 'schoolClass', 'academicYear']);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nis', 'like', "%{$search}%")
                   ->orWhereHas('user', function ($uq) use ($search) {
                       $uq->where('name', 'like', "%{$search}%");
                   });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $enrollments = $query->latest()->paginate($perPage)->withQueryString();
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.enrollments.index', compact('enrollments', 'classes', 'academicYears', 'selectedYearId'));
    }

    public function create(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $students = Student::with('user')->get();
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.enrollments.create', compact('students', 'classes', 'academicYears', 'activeYear'));
    }

    public function store(StudentEnrollmentRequest $request): RedirectResponse
    {
        $enrollment = StudentEnrollment::create($request->validated());

        ActivityLog::log(
            'create',
            'StudentEnrollment',
            $enrollment->id,
            "Mendaftarkan siswa {$enrollment->student->user->name} ke kelas {$enrollment->schoolClass->name} ({$enrollment->academicYear->name})."
        );

        return redirect()->route('admin.enrollments.index', [
            'academic_year_id' => $enrollment->academic_year_id,
            'class_id' => $enrollment->class_id,
        ])->with('success', 'Penempatan siswa ke rombel berhasil disimpan.');
    }

    public function edit(StudentEnrollment $enrollment): View
    {
        $enrollment->load(['student.user', 'schoolClass', 'academicYear']);
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'classes', 'academicYears'));
    }

    public function update(StudentEnrollmentRequest $request, StudentEnrollment $enrollment): RedirectResponse
    {
        $enrollment->update($request->validated());

        ActivityLog::log(
            'update',
            'StudentEnrollment',
            $enrollment->id,
            "Memperbarui penempatan siswa {$enrollment->student->user->name} ke kelas {$enrollment->schoolClass->name}."
        );

        return redirect()->route('admin.enrollments.index', [
            'academic_year_id' => $enrollment->academic_year_id,
            'class_id' => $enrollment->class_id,
        ])->with('success', 'Perubahan penempatan kelas berhasil disimpan.');
    }

    public function destroy(StudentEnrollment $enrollment): RedirectResponse
    {
        $studentName = $enrollment->student->user->name;
        $className = $enrollment->schoolClass->name;
        $yearId = $enrollment->academic_year_id;
        $id = $enrollment->id;

        $enrollment->delete();

        ActivityLog::log('delete', 'StudentEnrollment', $id, "Membatalkan penempatan siswa {$studentName} dari kelas {$className}.");

        return redirect()->route('admin.enrollments.index', ['academic_year_id' => $yearId])
            ->with('success', 'Penempatan siswa berhasil dibatalkan / dihapus.');
    }
}
