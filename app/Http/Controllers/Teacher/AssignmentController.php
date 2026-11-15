<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\AssignmentRequest;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentGrade;
use App\Models\ClassSubject;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $query = Assignment::with(['classSubject.schoolClass', 'classSubject.subject', 'creator'])
            ->withCount(['submissions', 'submissions as graded_submissions_count' => function ($q) {
                $q->has('grade');
            }])
            ->whereHas('classSubject', fn ($q) => $q->where('teacher_id', $teacher->id));

        if ($request->filled('class_subject_id')) {
            $query->where('class_subject_id', $request->input('class_subject_id'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $assignments = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('teacher.assignments.index', compact('assignments', 'myClassSubjects'));
    }

    public function create(): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        return view('teacher.assignments.create', compact('myClassSubjects'));
    }

    public function store(AssignmentRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();
        $validated = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('assignments', 'local');
        }

        $classSubject = ClassSubject::find($validated['class_subject_id']);

        $assignment = Assignment::create([
            'class_subject_id' => $classSubject->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'deadline' => $validated['deadline'],
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log(
            'create',
            'Assignment',
            $assignment->id,
            "Membuat tugas baru '{$assignment->title}' untuk kelas {$classSubject->schoolClass->name} mapel {$classSubject->subject->name}."
        );

        return redirect()->route('teacher.assignments.index', ['class_subject_id' => $classSubject->id])
            ->with('success', "Tugas '{$assignment->title}' berhasil dibuat.");
    }

    public function show(Assignment $assignment): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($assignment->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $assignment->load(['classSubject.schoolClass', 'classSubject.subject', 'submissions.student.user', 'submissions.grade']);

        return view('teacher.assignments.show', compact('assignment'));
    }

    public function downloadAttachment(Assignment $assignment): StreamedResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($assignment->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        abort_if(! $assignment->attachment_path || ! Storage::disk('local')->exists($assignment->attachment_path), 404, 'Lampiran tugas tidak ditemukan.');

        return Storage::disk('local')->download($assignment->attachment_path);
    }

    public function downloadSubmission(Submission $submission): StreamedResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($submission->assignment->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        abort_if(! $submission->file_path || ! Storage::disk('local')->exists($submission->file_path), 404, 'Berkas submission tidak ditemukan.');

        return Storage::disk('local')->download($submission->file_path);
    }

    public function gradeSubmission(Request $request, Submission $submission): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($submission->assignment->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ], [
            'score.required' => 'Nilai wajib diisi.',
            'score.numeric' => 'Nilai harus berupa angka antara 0 dan 100.',
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
        ]);

        AssignmentGrade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ]
        );

        ActivityLog::log(
            'update',
            'AssignmentGrade',
            $submission->id,
            "Memberikan nilai {$validated['score']} untuk tugas '{$submission->assignment->title}' siswa {$submission->student->user->name}."
        );

        return redirect()->route('teacher.assignments.show', $submission->assignment_id)
            ->with('success', "Nilai siswa {$submission->student->user->name} berhasil disimpan.");
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($assignment->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $id = $assignment->id;
        $title = $assignment->title;
        $classSubjectId = $assignment->class_subject_id;

        // Delete attachment
        if ($assignment->attachment_path && Storage::disk('local')->exists($assignment->attachment_path)) {
            Storage::disk('local')->delete($assignment->attachment_path);
        }

        // Delete submission files
        foreach ($assignment->submissions as $sub) {
            if ($sub->file_path && Storage::disk('local')->exists($sub->file_path)) {
                Storage::disk('local')->delete($sub->file_path);
            }
        }

        $assignment->delete();

        ActivityLog::log(
            'delete',
            'Assignment',
            $id,
            "Menghapus tugas '{$title}' beserta seluruh pengumpulan siswa."
        );

        return redirect()->route('teacher.assignments.index', ['class_subject_id' => $classSubjectId])
            ->with('success', "Tugas '{$title}' berhasil dihapus.");
    }
}
