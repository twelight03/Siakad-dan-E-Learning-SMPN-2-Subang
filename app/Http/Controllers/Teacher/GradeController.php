<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradeController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(GradeComponent $grade_component): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($grade_component->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $grade_component->load(['classSubject.schoolClass', 'classSubject.subject']);

        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_id', $grade_component->classSubject->class_id)
            ->where('academic_year_id', $grade_component->classSubject->academic_year_id)
            ->get();

        $existingGrades = Grade::where('grade_component_id', $grade_component->id)
            ->get()
            ->keyBy('student_id');

        return view('teacher.grades.input', compact('grade_component', 'enrollments', 'existingGrades'));
    }

    public function store(Request $request, GradeComponent $grade_component): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');
        abort_if($grade_component->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grades.*.feedback' => ['nullable', 'string', 'max:500'],
        ], [
            'grades.*.score.numeric' => 'Nilai harus berupa angka antara 0 hingga 100.',
            'grades.*.score.min' => 'Nilai minimal adalah 0.',
            'grades.*.score.max' => 'Nilai maksimal adalah 100.',
        ]);

        $enrolledStudentIds = StudentEnrollment::where('class_id', $grade_component->classSubject->class_id)
            ->where('academic_year_id', $grade_component->classSubject->academic_year_id)
            ->pluck('student_id')
            ->toArray();

        DB::transaction(function () use ($validated, $grade_component, $enrolledStudentIds) {
            $updatedCount = 0;
            foreach ($validated['grades'] as $item) {
                // Ensure student is actually enrolled in this class (anti-IDOR)
                if (! in_array($item['student_id'], $enrolledStudentIds)) {
                    continue;
                }

                if (isset($item['score']) && $item['score'] !== null && $item['score'] !== '') {
                    Grade::updateOrCreate(
                        [
                            'grade_component_id' => $grade_component->id,
                            'student_id' => $item['student_id'],
                        ],
                        [
                            'score' => $item['score'],
                            'feedback' => $item['feedback'] ?? null,
                            'graded_by' => auth()->id(),
                        ]
                    );
                    $updatedCount++;
                }
            }

            ActivityLog::log(
                'update',
                'Grade',
                $grade_component->id,
                "Memperbarui {$updatedCount} nilai siswa untuk komponen {$grade_component->name} kelas {$grade_component->classSubject->schoolClass->name}."
            );
        });

        return redirect()->route('teacher.grades.index', $grade_component->id)
            ->with('success', 'Nilai siswa berhasil disimpan.');
    }
}
