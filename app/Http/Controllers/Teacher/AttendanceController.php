<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\AttendanceSessionRequest;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $query = AttendanceSession::with(['classSubject.schoolClass', 'classSubject.subject'])
            ->whereHas('classSubject', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            });

        if ($request->filled('class_subject_id')) {
            $query->where('class_subject_id', $request->input('class_subject_id'));
        }

        if ($request->filled('session_date')) {
            $query->where('session_date', $request->input('session_date'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $sessions = $query->latest('session_date')->paginate($perPage)->withQueryString();

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        return view('teacher.attendances.index', compact('sessions', 'myClassSubjects'));
    }

    public function create(Request $request): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $selectedClassSubject = null;
        $students = collect();

        if ($request->filled('class_subject_id')) {
            $selectedClassSubject = $myClassSubjects->firstWhere('id', $request->input('class_subject_id'));
            abort_if(! $selectedClassSubject, 403, 'Anda tidak ditugaskan pada kelas ini.');

            $students = StudentEnrollment::with('student.user')
                ->where('class_id', $selectedClassSubject->class_id)
                ->where('academic_year_id', $selectedClassSubject->academic_year_id)
                ->get()
                ->pluck('student');
        }

        return view('teacher.attendances.create', compact('myClassSubjects', 'selectedClassSubject', 'students'));
    }

    public function store(AttendanceSessionRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();
        $validated = $request->validated();

        $classSubject = ClassSubject::where('id', $validated['class_subject_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        DB::transaction(function () use ($validated, $classSubject) {
            $session = AttendanceSession::create([
                'class_subject_id' => $classSubject->id,
                'session_date' => $validated['session_date'],
                'topic' => $validated['topic'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['attendances'] as $att) {
                Attendance::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $att['student_id'],
                    'status' => $att['status'],
                    'note' => $att['note'] ?? null,
                    'recorded_by' => auth()->id(),
                ]);
            }

            ActivityLog::log(
                'create',
                'AttendanceSession',
                $session->id,
                "Mencatat sesi absensi kelas {$classSubject->schoolClass->name} mapel {$classSubject->subject->name} tanggal {$session->session_date->format('d/m/Y')}."
            );
        });

        return redirect()->route('teacher.attendances.index', ['class_subject_id' => $classSubject->id])
            ->with('success', 'Sesi absensi dan catatan kehadiran siswa berhasil disimpan.');
    }

    public function show(AttendanceSession $attendance): View
    {
        $teacher = $this->getTeacher();
        abort_if($attendance->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $attendance->load(['classSubject.schoolClass', 'classSubject.subject', 'attendances.student.user']);

        return view('teacher.attendances.show', ['session' => $attendance]);
    }

    public function edit(AttendanceSession $attendance): View
    {
        $teacher = $this->getTeacher();
        abort_if($attendance->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $attendance->load(['classSubject.schoolClass', 'classSubject.subject', 'attendances.student.user']);

        return view('teacher.attendances.edit', ['session' => $attendance]);
    }

    public function update(Request $request, AttendanceSession $attendance): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if($attendance->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $request->validate([
            'session_date' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', 'in:present,excused,sick,absent'],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $attendance) {
            $attendance->update([
                'session_date' => $request->input('session_date'),
                'topic' => $request->input('topic'),
            ]);

            foreach ($request->input('attendances') as $attId => $attData) {
                Attendance::where('id', $attId)
                    ->where('attendance_session_id', $attendance->id)
                    ->update([
                        'status' => $attData['status'],
                        'note' => $attData['note'] ?? null,
                    ]);
            }

            ActivityLog::log(
                'update',
                'AttendanceSession',
                $attendance->id,
                "Memperbarui catatan absensi sesi tanggal {$attendance->session_date->format('d/m/Y')}."
            );
        });

        return redirect()->route('teacher.attendances.show', $attendance->id)
            ->with('success', 'Catatan absensi berhasil diperbarui.');
    }

    public function destroy(AttendanceSession $attendance): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if($attendance->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $id = $attendance->id;
        $classSubjectId = $attendance->class_subject_id;
        $date = $attendance->session_date->format('d/m/Y');

        $attendance->delete();

        ActivityLog::log('delete', 'AttendanceSession', $id, "Menghapus sesi absensi tanggal {$date}.");

        return redirect()->route('teacher.attendances.index', ['class_subject_id' => $classSubjectId])
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }
}
