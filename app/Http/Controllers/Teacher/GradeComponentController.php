<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\GradeComponentRequest;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\GradeComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeComponentController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(Request $request): View
    {
        $teacher = $this->getTeacher();
        abort_if(! $teacher, 403, 'Akses khusus guru.');

        $myClassSubjects = ClassSubject::with(['schoolClass', 'subject', 'gradeComponents.grades'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $selectedClassSubject = null;
        if ($request->filled('class_subject_id')) {
            $selectedClassSubject = $myClassSubjects->firstWhere('id', $request->input('class_subject_id'));
        } else {
            $selectedClassSubject = $myClassSubjects->first();
        }

        $components = collect();
        $totalWeight = 0;
        if ($selectedClassSubject) {
            $components = GradeComponent::where('class_subject_id', $selectedClassSubject->id)
                ->withCount('grades')
                ->get();
            $totalWeight = $components->sum('weight');
        }

        return view('teacher.grades.components.index', compact('myClassSubjects', 'selectedClassSubject', 'components', 'totalWeight'));
    }

    public function store(GradeComponentRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();
        $validated = $request->validated();

        $classSubject = ClassSubject::where('id', $validated['class_subject_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $component = GradeComponent::create([
            'class_subject_id' => $classSubject->id,
            'name' => $validated['name'],
            'weight' => $validated['weight'],
            'max_score' => $validated['max_score'] ?? 100.00,
        ]);

        ActivityLog::log(
            'create',
            'GradeComponent',
            $component->id,
            "Membuat komponen penilaian '{$component->name}' bobot {$component->weight}% untuk kelas {$classSubject->schoolClass->name} mapel {$classSubject->subject->name}."
        );

        return redirect()->route('teacher.grade-components.index', ['class_subject_id' => $classSubject->id])
            ->with('success', "Komponen penilaian '{$component->name}' berhasil ditambahkan.");
    }

    public function update(GradeComponentRequest $request, GradeComponent $grade_component): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if($grade_component->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $validated = $request->validated();

        $oldWeight = $grade_component->weight;
        $oldName = $grade_component->name;

        $grade_component->update([
            'name' => $validated['name'],
            'weight' => $validated['weight'],
            'max_score' => $validated['max_score'] ?? 100.00,
        ]);

        ActivityLog::log(
            'update',
            'GradeComponent',
            $grade_component->id,
            "Memperbarui komponen penilaian '{$oldName}' -> '{$grade_component->name}' (bobot: {$oldWeight}% -> {$grade_component->weight}%)."
        );

        return redirect()->route('teacher.grade-components.index', ['class_subject_id' => $grade_component->class_subject_id])
            ->with('success', "Komponen penilaian '{$grade_component->name}' berhasil diperbarui.");
    }

    public function destroy(GradeComponent $grade_component): RedirectResponse
    {
        $teacher = $this->getTeacher();
        abort_if($grade_component->classSubject->teacher_id !== $teacher->id, 403, 'Akses ditolak.');

        $classSubjectId = $grade_component->class_subject_id;
        $name = $grade_component->name;
        $id = $grade_component->id;

        $grade_component->delete();

        ActivityLog::log(
            'delete',
            'GradeComponent',
            $id,
            "Menghapus komponen penilaian '{$name}' berserta nilainya."
        );

        return redirect()->route('teacher.grade-components.index', ['class_subject_id' => $classSubjectId])
            ->with('success', "Komponen penilaian '{$name}' berhasil dihapus.");
    }
}
