<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScheduleRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\ScheduleSetting;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display the hierarchical schedule view (Tingkatan -> Kelas -> Matriks Jadwal Mingguan).
     */
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);

        $gradeLevels = [
            '7' => 'Tingkat VII (Kelas 7)',
            '8' => 'Tingkat VIII (Kelas 8)',
            '9' => 'Tingkat IX (Kelas 9)',
        ];

        // Determine selected grade and class
        $selectedClassId = $request->input('class_id');
        $selectedClass = $selectedClassId ? SchoolClass::with('homeroomTeacher.user')->find($selectedClassId) : null;

        $selectedGrade = $request->input('grade_level', $selectedClass?->grade_level ?? '7');
        if (! in_array($selectedGrade, ['7', '8', '9'])) {
            $selectedGrade = '7';
        }

        $classesInGrade = SchoolClass::where('grade_level', $selectedGrade)
            ->withCount(['enrollments' => function ($q) use ($selectedYearId) {
                if ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                }
            }])
            ->with('homeroomTeacher.user')
            ->orderBy('name')
            ->get();

        if (! $selectedClass || $selectedClass->grade_level !== $selectedGrade) {
            $selectedClass = $classesInGrade->first();
        }

        // Fetch schedules for the selected class (Matrix View)
        $matrixSchedules = collect();
        $scheduleMatrix = [];

        if ($selectedClass) {
            $matrixSchedules = Schedule::with(['classSubject.subject', 'classSubject.teacher.user'])
                ->whereHas('classSubject', function ($q) use ($selectedClass, $selectedYearId) {
                    $q->where('class_id', $selectedClass->id);
                    if ($selectedYearId) {
                        $q->where('academic_year_id', $selectedYearId);
                    }
                })
                ->orderBy('start_time')
                ->get();

            foreach ($matrixSchedules as $ms) {
                $st = substr($ms->start_time, 0, 5);
                $scheduleMatrix[$ms->day_of_week][$st] = $ms;
            }
        }

        // Get configurable timeline slots & recesses
        $timelineMonday = ScheduleSetting::getDayTimeline('monday');
        $timelineFriday = ScheduleSetting::getDayTimeline('friday');
        $scheduleConfig = ScheduleSetting::getScheduleConfig();

        // General query for search/filter and backwards compatibility
        $query = Schedule::with(['classSubject.schoolClass', 'classSubject.subject', 'classSubject.teacher.user', 'classSubject.academicYear']);
        if ($selectedYearId) {
            $query->whereHas('classSubject', function ($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            });
        }
        if ($selectedClass) {
            $query->whereHas('classSubject', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass->id);
            });
        }
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->input('day_of_week'));
        }

        $perPage = 50;
        $allSchedules = (clone $query)->orderByRaw("CASE day_of_week WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 ELSE 6 END")
            ->orderBy('start_time')
            ->get();

        $schedules = (clone $query)->orderByRaw("CASE day_of_week WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 ELSE 6 END")
            ->orderBy('start_time')
            ->paginate($perPage)
            ->withQueryString();

        $schedulesByDay = $allSchedules->groupBy('day_of_week');

        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $daysMap = [
            'monday' => ['label' => 'Senin', 'color' => 'blue', 'border' => 'border-blue-500', 'badge' => 'bg-blue-100 text-blue-800'],
            'tuesday' => ['label' => 'Selasa', 'color' => 'emerald', 'border' => 'border-emerald-500', 'badge' => 'bg-emerald-100 text-emerald-800'],
            'wednesday' => ['label' => 'Rabu', 'color' => 'amber', 'border' => 'border-amber-500', 'badge' => 'bg-amber-100 text-amber-800'],
            'thursday' => ['label' => 'Kamis', 'color' => 'purple', 'border' => 'border-purple-500', 'badge' => 'bg-purple-100 text-purple-800'],
            'friday' => ['label' => 'Jumat', 'color' => 'teal', 'border' => 'border-teal-500', 'badge' => 'bg-teal-100 text-teal-800'],
        ];

        return view('admin.schedules.index', compact(
            'schedules',
            'schedulesByDay',
            'classes',
            'academicYears',
            'selectedYearId',
            'daysMap',
            'gradeLevels',
            'selectedGrade',
            'classesInGrade',
            'selectedClass',
            'matrixSchedules',
            'scheduleMatrix',
            'timelineMonday',
            'timelineFriday',
            'scheduleConfig'
        ));
    }

    public function create(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user'])
            ->where('academic_year_id', $activeYear?->id)
            ->get();

        return view('admin.schedules.create', compact('classSubjects', 'activeYear'));
    }

    public function store(ScheduleRequest $request): RedirectResponse
    {
        $schedule = Schedule::create($request->validated());

        ActivityLog::log('create', 'Schedule', $schedule->id, "Menambahkan jadwal pelajaran ID {$schedule->id}.");

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user'])
            ->where('academic_year_id', $activeYear?->id)
            ->get();

        return view('admin.schedules.edit', compact('schedule', 'classSubjects', 'activeYear'));
    }

    public function update(ScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        ActivityLog::log('update', 'Schedule', $schedule->id, "Memperbarui jadwal pelajaran ID {$schedule->id}.");

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $id = $schedule->id;
        $schedule->delete();

        ActivityLog::log('delete', 'Schedule', $id, "Menghapus jadwal pelajaran ID {$id}.");

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    /**
     * Display the time and recess configuration panel for operator.
     */
    public function settings(): View
    {
        $config = ScheduleSetting::getScheduleConfig();

        return view('admin.schedules.settings', compact('config'));
    }

    /**
     * Save the time and recess configuration.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_duration' => ['required', 'integer', 'min:20', 'max:90'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'friday_end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'recess_names' => ['nullable', 'array'],
            'recess_starts' => ['nullable', 'array'],
            'recess_ends' => ['nullable', 'array'],
            'friday_recess_names' => ['nullable', 'array'],
            'friday_recess_starts' => ['nullable', 'array'],
            'friday_recess_ends' => ['nullable', 'array'],
        ]);

        ScheduleSetting::set('lesson_duration', (int) $validated['lesson_duration']);
        ScheduleSetting::set('start_time', $validated['start_time']);
        ScheduleSetting::set('end_time', $validated['end_time']);
        ScheduleSetting::set('friday_end_time', $validated['friday_end_time']);

        // Build Monday-Thursday recesses array
        $recesses = [];
        if (! empty($validated['recess_names'])) {
            foreach ($validated['recess_names'] as $i => $name) {
                $start = $validated['recess_starts'][$i] ?? null;
                $end = $validated['recess_ends'][$i] ?? null;
                if ($name && $start && $end) {
                    $recesses[] = [
                        'name' => $name,
                        'start' => $start,
                        'end' => $end,
                    ];
                }
            }
        }
        ScheduleSetting::set('recesses', $recesses);

        // Build Friday recesses array
        $fridayRecesses = [];
        if (! empty($validated['friday_recess_names'])) {
            foreach ($validated['friday_recess_names'] as $i => $name) {
                $start = $validated['friday_recess_starts'][$i] ?? null;
                $end = $validated['friday_recess_ends'][$i] ?? null;
                if ($name && $start && $end) {
                    $fridayRecesses[] = [
                        'name' => $name,
                        'start' => $start,
                        'end' => $end,
                    ];
                }
            }
        }
        ScheduleSetting::set('friday_recesses', $fridayRecesses);

        ActivityLog::log('update', 'ScheduleSetting', null, "Memperbarui konfigurasi jam pelajaran & istirahat sekolah.");

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Pengaturan jam pelajaran dan istirahat berhasil diperbarui!');
    }
}
