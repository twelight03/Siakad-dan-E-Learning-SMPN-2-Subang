<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupGeneratorController extends Controller
{
    /**
     * Display the group generator form and results.
     */
    public function index(Request $request): View
    {
        $teacher = auth()->user()->teacher;
        $activeYear = AcademicYear::where('is_active', true)->first();

        $classSubjects = $teacher
            ? ClassSubject::with(['schoolClass', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->when($activeYear, function ($q) use ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                })
                ->get()
            : collect();

        $selectedClassSubjectId = $request->input('class_subject_id', $classSubjects->first()?->id);
        $selectedClassSubject = $classSubjects->firstWhere('id', $selectedClassSubjectId);

        $students = collect();
        if ($selectedClassSubject && $activeYear) {
            $students = Student::with('user')
                ->whereHas('enrollments', function ($q) use ($selectedClassSubject, $activeYear) {
                    $q->where('class_id', $selectedClassSubject->class_id)
                      ->where('academic_year_id', $activeYear->id);
                })
                ->get();
        }

        $groups = null;

        if ($request->has('generate') && $students->isNotEmpty()) {
            $splitBy = $request->input('split_by', 'num_groups');
            $balanceGender = $request->boolean('balance_gender', true);
            $theme = $request->input('naming_theme', 'numbers');

            $numGroups = 4;
            if ($splitBy === 'num_groups') {
                $numGroups = max(2, min((int) $request->input('num_groups', 4), $students->count()));
            } else {
                $studentsPerGroup = max(2, (int) $request->input('students_per_group', 4));
                $numGroups = max(2, (int) ceil($students->count() / $studentsPerGroup));
            }

            $groups = $this->createGroups($students, $numGroups, $balanceGender, $theme);
        }

        return view('teacher.group-generator.index', compact(
            'classSubjects',
            'selectedClassSubject',
            'students',
            'groups',
            'activeYear'
        ));
    }

    /**
     * Create shuffled group arrays.
     */
    private function createGroups($students, int $numGroups, bool $balanceGender, string $theme): array
    {
        $themeNames = [
            'numbers' => fn ($i) => "Kelompok ".($i + 1),
            'birds' => function ($i) {
                $names = ['Garuda', 'Rajawali', 'Elang', 'Merpati', 'Cenderawasih', 'Kasuari', 'Alap-alap', 'Kutilang', 'Kenari', 'Gelatik'];
                return "Kelompok ".($names[$i % count($names)] ?? 'Tim '.($i + 1));
            },
            'colors' => function ($i) {
                $names = ['Merah', 'Biru', 'Hijau', 'Kuning', 'Ungu', 'Oranye', 'Toska', 'Emas', 'Perak', 'Cokelat'];
                return "Kelompok ".($names[$i % count($names)] ?? 'Warna '.($i + 1));
            },
        ];

        $namer = $themeNames[$theme] ?? $themeNames['numbers'];

        $buckets = [];
        for ($i = 0; $i < $numGroups; $i++) {
            $buckets[$i] = [
                'name' => $namer($i),
                'students' => [],
                'male_count' => 0,
                'female_count' => 0,
            ];
        }

        if ($balanceGender) {
            $males = $students->where('gender', 'male')->shuffle()->values();
            $females = $students->where('gender', 'female')->shuffle()->values();

            $idx = 0;
            foreach ($males as $m) {
                $target = $idx % $numGroups;
                $buckets[$target]['students'][] = $m;
                $buckets[$target]['male_count']++;
                $idx++;
            }

            $idx = 0;
            foreach ($females as $f) {
                $target = $idx % $numGroups;
                $buckets[$target]['students'][] = $f;
                $buckets[$target]['female_count']++;
                $idx++;
            }
        } else {
            $shuffled = $students->shuffle()->values();
            $idx = 0;
            foreach ($shuffled as $s) {
                $target = $idx % $numGroups;
                $buckets[$target]['students'][] = $s;
                if ($s->gender === 'male') {
                    $buckets[$target]['male_count']++;
                } else {
                    $buckets[$target]['female_count']++;
                }
                $idx++;
            }
        }

        return $buckets;
    }
}
