<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Grade;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $currentEnrollment = $student->currentEnrollment;
        $subjectReports = collect();

        if ($currentEnrollment) {
            $classSubjects = ClassSubject::with(['subject', 'teacher.user', 'gradeComponents'])
                ->where('class_id', $currentEnrollment->class_id)
                ->where('academic_year_id', $currentEnrollment->academic_year_id)
                ->get();

            $studentGrades = Grade::where('student_id', $student->id)
                ->whereIn('grade_component_id', $classSubjects->flatMap->gradeComponents->pluck('id'))
                ->get()
                ->keyBy('grade_component_id');

            $subjectReports = $classSubjects->map(function ($cs) use ($studentGrades) {
                $components = $cs->gradeComponents->map(function ($comp) use ($studentGrades) {
                    $grade = $studentGrades->get($comp->id);
                    return [
                        'name' => $comp->name,
                        'weight' => (float) $comp->weight,
                        'max_score' => (float) $comp->max_score,
                        'score' => $grade ? (float) $grade->score : null,
                        'feedback' => $grade?->feedback,
                    ];
                });

                $totalWeight = $components->whereNotNull('score')->sum('weight');
                $weightedScore = 0;
                if ($totalWeight > 0) {
                    $weightedSum = $components->whereNotNull('score')->sum(function ($c) {
                        return $c['score'] * ($c['weight'] / 100);
                    });
                    $allWeights = $components->sum('weight');
                    $weightedScore = $allWeights > 0 ? round(($weightedSum / ($allWeights / 100)), 2) : 0;
                }

                $kkm = $cs->subject->kkm;
                $isPassed = $weightedScore >= $kkm;

                $predicate = 'D';
                if ($weightedScore >= 90) {
                    $predicate = 'A';
                } elseif ($weightedScore >= 80) {
                    $predicate = 'B';
                } elseif ($weightedScore >= 70) {
                    $predicate = 'C';
                }

                return [
                    'classSubject' => $cs,
                    'subject' => $cs->subject,
                    'teacher' => $cs->teacher->user->name,
                    'kkm' => $kkm,
                    'components' => $components,
                    'finalScore' => $weightedScore,
                    'isPassed' => $isPassed,
                    'predicate' => $predicate,
                ];
            });
        }

        return view('student.grades.index', compact('student', 'currentEnrollment', 'subjectReports'));
    }
}
