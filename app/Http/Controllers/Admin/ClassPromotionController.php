<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassPromotionController extends Controller
{
    /**
     * Display the promotion and student distribution dashboard.
     */
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classes = SchoolClass::withCount(['enrollments' => function ($q) use ($activeYear) {
            if ($activeYear) {
                $q->where('academic_year_id', $activeYear->id);
            }
        }])->orderBy('grade_level')->orderBy('name')->get();

        return view('admin.promotions.index', compact('academicYears', 'activeYear', 'classes'));
    }

    /**
     * Fetch students of specific classes for preview.
     */
    public function getStudents(Request $request): JsonResponse
    {
        $classIds = (array) $request->input('class_ids', []);
        $yearId = $request->input('academic_year_id');

        $students = Student::with('user')
            ->whereHas('enrollments', function ($q) use ($classIds, $yearId) {
                $q->whereIn('class_id', $classIds);
                if ($yearId) {
                    $q->where('academic_year_id', $yearId);
                }
            })
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->user->name,
                    'nis' => $s->nis,
                    'gender' => $s->gender,
                ];
            });

        return response()->json([
            'count' => $students->count(),
            'male_count' => $students->where('gender', 'male')->count(),
            'female_count' => $students->where('gender', 'female')->count(),
            'students' => $students,
        ]);
    }

    /**
     * Mode 1: Direct Promotion of 1 Whole Class.
     */
    public function directPromotion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_class_id' => ['required', 'exists:classes,id'],
            'from_academic_year_id' => ['required', 'exists:academic_years,id'],
            'to_class_id' => ['required', 'exists:classes,id', 'different:from_class_id'],
            'to_academic_year_id' => ['required', 'exists:academic_years,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ], [
            'to_class_id.different' => 'Kelas tujuan harus berbeda dari kelas asal.',
        ]);

        $fromClass = SchoolClass::findOrFail($validated['from_class_id']);
        $toClass = SchoolClass::findOrFail($validated['to_class_id']);
        $toYear = AcademicYear::findOrFail($validated['to_academic_year_id']);

        // If specific students selected, use them. Otherwise promote all enrolled students from that class
        $studentIds = $validated['student_ids'] ?? [];
        if (empty($studentIds)) {
            $studentIds = StudentEnrollment::where('class_id', $fromClass->id)
                ->where('academic_year_id', $validated['from_academic_year_id'])
                ->pluck('student_id')
                ->toArray();
        }

        if (empty($studentIds)) {
            return back()->with('error', 'Tidak ada siswa yang ditemukan di kelas asal untuk dinaikkan.');
        }

        DB::transaction(function () use ($studentIds, $fromClass, $toClass, $toYear) {
            foreach ($studentIds as $sId) {
                StudentEnrollment::updateOrCreate(
                    [
                        'student_id' => $sId,
                        'academic_year_id' => $toYear->id,
                    ],
                    [
                        'class_id' => $toClass->id,
                    ]
                );
            }

            ActivityLog::log(
                'promote_class_direct',
                'classes',
                $toClass->id,
                "Menaikkan massal ".count($studentIds)." siswa dari kelas {$fromClass->name} ke {$toClass->name} (T.A. {$toYear->name})"
            );
        });

        return back()->with('success', 'Berhasil menaikkan '.count($studentIds)." siswa dari {$fromClass->name} ke {$toClass->name}!");
    }

    /**
     * Mode 2: Calculate random/balanced distribution preview.
     */
    public function previewDistribution(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_class_ids' => ['required', 'array', 'min:1'],
            'from_class_ids.*' => ['exists:classes,id'],
            'from_academic_year_id' => ['required', 'exists:academic_years,id'],
            'to_class_ids' => ['required', 'array', 'min:1'],
            'to_class_ids.*' => ['exists:classes,id'],
            'balance_gender' => ['nullable', 'boolean'],
            'quota_per_class' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $result = $this->computeDistribution(
            $validated['from_class_ids'],
            $validated['from_academic_year_id'],
            $validated['to_class_ids'],
            $request->boolean('balance_gender', true),
            $validated['quota_per_class'] ?? null
        );

        return response()->json($result);
    }

    /**
     * Mode 2: Save and apply random/balanced distribution.
     */
    public function applyDistribution(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_class_ids' => ['required', 'array', 'min:1'],
            'from_class_ids.*' => ['exists:classes,id'],
            'from_academic_year_id' => ['required', 'exists:academic_years,id'],
            'to_class_ids' => ['required', 'array', 'min:1'],
            'to_class_ids.*' => ['exists:classes,id'],
            'to_academic_year_id' => ['required', 'exists:academic_years,id'],
            'balance_gender' => ['nullable', 'boolean'],
            'quota_per_class' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $computed = $this->computeDistribution(
            $validated['from_class_ids'],
            $validated['from_academic_year_id'],
            $validated['to_class_ids'],
            $request->boolean('balance_gender', true),
            $validated['quota_per_class'] ?? null
        );

        $toYearId = $validated['to_academic_year_id'];
        $totalMoved = 0;

        DB::transaction(function () use ($computed, $toYearId, &$totalMoved) {
            foreach ($computed['distribution'] as $item) {
                $targetClassId = $item['class']['id'];
                foreach ($item['students'] as $student) {
                    StudentEnrollment::updateOrCreate(
                        [
                            'student_id' => $student['id'],
                            'academic_year_id' => $toYearId,
                        ],
                        [
                            'class_id' => $targetClassId,
                        ]
                    );
                    $totalMoved++;
                }
            }

            ActivityLog::log(
                'distribute_students_random',
                'student_enrollments',
                null,
                "Mendistribusikan & mengacak {$totalMoved} siswa ke ".count($computed['distribution'])." kelas tingkat berikutnya"
            );
        });

        return back()->with('success', "Berhasil mendistribusikan dan mengacak {$totalMoved} siswa ke kelas tujuan dengan seimbang!");
    }

    /**
     * Internal distribution logic.
     */
    private function computeDistribution(array $fromClassIds, int $fromYearId, array $toClassIds, bool $balanceGender, ?int $quota): array
    {
        $targetClasses = SchoolClass::whereIn('id', $toClassIds)->orderBy('name')->get();
        $targetCount = $targetClasses->count();

        $students = Student::with('user')
            ->whereHas('enrollments', function ($q) use ($fromClassIds, $fromYearId) {
                $q->whereIn('class_id', $fromClassIds)->where('academic_year_id', $fromYearId);
            })
            ->get();

        $buckets = [];
        foreach ($targetClasses as $c) {
            $buckets[$c->id] = [
                'class' => ['id' => $c->id, 'name' => $c->name, 'grade_level' => $c->grade_level],
                'students' => [],
                'male_count' => 0,
                'female_count' => 0,
            ];
        }

        $classKeys = array_keys($buckets);

        if ($balanceGender) {
            $males = $students->where('gender', 'male')->shuffle()->values();
            $females = $students->where('gender', 'female')->shuffle()->values();

            $targetIdx = 0;
            foreach ($males as $m) {
                $targetClassId = $classKeys[$targetIdx % $targetCount];
                $buckets[$targetClassId]['students'][] = [
                    'id' => $m->id,
                    'name' => $m->user->name,
                    'nis' => $m->nis,
                    'gender' => $m->gender,
                ];
                $buckets[$targetClassId]['male_count']++;
                $targetIdx++;
            }

            $targetIdx = 0;
            foreach ($females as $f) {
                $targetClassId = $classKeys[$targetIdx % $targetCount];
                $buckets[$targetClassId]['students'][] = [
                    'id' => $f->id,
                    'name' => $f->user->name,
                    'nis' => $f->nis,
                    'gender' => $f->gender,
                ];
                $buckets[$targetClassId]['female_count']++;
                $targetIdx++;
            }
        } else {
            $shuffled = $students->shuffle()->values();
            $targetIdx = 0;
            foreach ($shuffled as $s) {
                $targetClassId = $classKeys[$targetIdx % $targetCount];
                $buckets[$targetClassId]['students'][] = [
                    'id' => $s->id,
                    'name' => $s->user->name,
                    'nis' => $s->nis,
                    'gender' => $s->gender,
                ];
                if ($s->gender === 'male') {
                    $buckets[$targetClassId]['male_count']++;
                } else {
                    $buckets[$targetClassId]['female_count']++;
                }
                $targetIdx++;
            }
        }

        return [
            'total_students' => $students->count(),
            'total_target_classes' => $targetCount,
            'distribution' => array_values($buckets),
        ];
    }
}
