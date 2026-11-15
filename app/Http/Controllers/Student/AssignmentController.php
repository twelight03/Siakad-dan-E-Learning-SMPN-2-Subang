<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmissionRequest;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\StudentEnrollment;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    public function index(Request $request): View
    {
        $student = $this->getStudent();
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $currentEnrollment = $student->currentEnrollment;
        $assignments = collect();

        if ($currentEnrollment) {
            $query = Assignment::with(['classSubject.subject', 'creator', 'submissions' => function ($q) use ($student) {
                $q->where('student_id', $student->id)->with('grade');
            }])
                ->whereHas('classSubject', function ($q) use ($currentEnrollment) {
                    $q->where('class_id', $currentEnrollment->class_id)
                      ->where('academic_year_id', $currentEnrollment->academic_year_id);
                });

            if ($request->filled('subject_id')) {
                $query->whereHas('classSubject', fn ($q) => $q->where('subject_id', $request->input('subject_id')));
            }

            $perPage = (int) $request->input('per_page', 10);
            if (! in_array($perPage, [10, 25, 50, 100])) {
                $perPage = 10;
            }

            $assignments = $query->latest('deadline')->paginate($perPage)->withQueryString();
        }

        return view('student.assignments.index', compact('assignments', 'currentEnrollment', 'student'));
    }

    public function show(Assignment $assignment): View
    {
        $student = $this->getStudent();
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $isEnrolled = StudentEnrollment::where('student_id', $student->id)
            ->where('class_id', $assignment->classSubject->class_id)
            ->where('academic_year_id', $assignment->classSubject->academic_year_id)
            ->exists();

        abort_if(! $isEnrolled, 403, 'Anda tidak terdaftar di kelas tugas ini.');

        $assignment->load(['classSubject.schoolClass', 'classSubject.subject', 'creator']);

        $submission = Submission::with('grade.grader')
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission', 'student'));
    }

    public function submit(SubmissionRequest $request, Assignment $assignment): RedirectResponse
    {
        $student = $this->getStudent();
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $validated = $request->validated();

        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $filePath = $existingSubmission?->file_path;
        if ($request->hasFile('submission_file')) {
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }
            $filePath = $request->file('submission_file')->store('submissions', 'local');
        }

        $isLate = now()->gt($assignment->deadline);
        $status = $isLate ? 'late' : 'submitted';

        $submission = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $filePath,
                'text_answer' => $validated['text_answer'] ?? $existingSubmission?->text_answer,
                'submitted_at' => now(),
                'status' => $status,
            ]
        );

        ActivityLog::log(
            'create',
            'Submission',
            $submission->id,
            "Siswa {$student->user->name} mengumpulkan tugas '{$assignment->title}' (status: {$status})."
        );

        $msg = $isLate
            ? 'Tugas berhasil dikumpulkan (Terlambat melewati batas waktu).'
            : 'Tugas berhasil dikumpulkan tepat waktu.';

        return redirect()->route('student.assignments.show', $assignment->id)
            ->with('success', $msg);
    }

    public function downloadAttachment(Assignment $assignment): StreamedResponse
    {
        $student = $this->getStudent();
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $isEnrolled = StudentEnrollment::where('student_id', $student->id)
            ->where('class_id', $assignment->classSubject->class_id)
            ->where('academic_year_id', $assignment->classSubject->academic_year_id)
            ->exists();

        abort_if(! $isEnrolled, 403, 'Anda tidak memiliki akses ke berkas tugas ini.');
        abort_if(! $assignment->attachment_path || ! Storage::disk('local')->exists($assignment->attachment_path), 404, 'Lampiran tugas tidak ditemukan.');

        return Storage::disk('local')->download($assignment->attachment_path);
    }

    public function downloadMySubmission(Submission $submission): StreamedResponse
    {
        $student = $this->getStudent();
        abort_if(! $student, 403, 'Akses khusus siswa.');

        // IDOR Prevention: Must belong to authenticated student
        abort_if($submission->student_id !== $student->id, 403, 'Akses ditolak. Anda tidak berhak mengunduh submission siswa lain.');
        abort_if(! $submission->file_path || ! Storage::disk('local')->exists($submission->file_path), 404, 'Berkas submission tidak ditemukan.');

        return Storage::disk('local')->download($submission->file_path);
    }
}
