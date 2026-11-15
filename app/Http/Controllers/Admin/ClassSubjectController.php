<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClassSubjectRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassSubjectController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);

        $query = ClassSubject::with(['schoolClass', 'subject', 'teacher.user', 'academicYear']);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('subject', function ($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%")
                   ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $classSubjects = $query->latest()->paginate($perPage)->withQueryString();
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $teachers = Teacher::with('user')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.class-subjects.index', compact('classSubjects', 'classes', 'teachers', 'academicYears', 'selectedYearId'));
    }

    public function create(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = Teacher::with('user')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.class-subjects.create', compact('classes', 'subjects', 'teachers', 'academicYears', 'activeYear'));
    }

    public function store(ClassSubjectRequest $request): RedirectResponse
    {
        $cs = ClassSubject::create($request->validated());

        ActivityLog::log(
            'create',
            'ClassSubject',
            $cs->id,
            "Menugaskan guru {$cs->teacher->user->name} untuk mapel {$cs->subject->name} pada kelas {$cs->schoolClass->name}."
        );

        return redirect()->route('admin.class-subjects.index', [
            'academic_year_id' => $cs->academic_year_id,
            'class_id' => $cs->class_id,
        ])->with('success', 'Penugasan guru ke kelas & mapel berhasil disimpan.');
    }

    public function edit(ClassSubject $classSubject): View
    {
        $classSubject->load(['schoolClass', 'subject', 'teacher.user', 'academicYear']);
        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = Teacher::with('user')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.class-subjects.edit', compact('classSubject', 'classes', 'subjects', 'teachers', 'academicYears'));
    }

    public function update(ClassSubjectRequest $request, ClassSubject $classSubject): RedirectResponse
    {
        $classSubject->update($request->validated());

        ActivityLog::log(
            'update',
            'ClassSubject',
            $classSubject->id,
            "Memperbarui penugasan guru {$classSubject->teacher->user->name} untuk mapel {$classSubject->subject->name} pada kelas {$classSubject->schoolClass->name}."
        );

        return redirect()->route('admin.class-subjects.index', [
            'academic_year_id' => $classSubject->academic_year_id,
            'class_id' => $classSubject->class_id,
        ])->with('success', 'Penugasan guru berhasil diperbarui.');
    }

    public function destroy(ClassSubject $classSubject): RedirectResponse
    {
        if (
            $classSubject->schedules()->exists() ||
            $classSubject->attendanceSessions()->exists() ||
            $classSubject->gradeComponents()->exists() ||
            $classSubject->materials()->exists() ||
            $classSubject->assignments()->exists()
        ) {
            return redirect()->route('admin.class-subjects.index')
                ->with('error', 'Penugasan guru ini tidak dapat dihapus karena sudah memiliki aktivitas akademik terkait (jadwal, absensi, komponen nilai, materi, atau tugas).');
        }

        $teacherName = $classSubject->teacher->user->name;
        $subjectName = $classSubject->subject->name;
        $className = $classSubject->schoolClass->name;
        $yearId = $classSubject->academic_year_id;
        $id = $classSubject->id;

        $classSubject->delete();

        ActivityLog::log('delete', 'ClassSubject', $id, "Menghapus penugasan {$teacherName} pada {$subjectName} ({$className}).");

        return redirect()->route('admin.class-subjects.index', ['academic_year_id' => $yearId])
            ->with('success', 'Penugasan guru berhasil dihapus.');
    }
}
