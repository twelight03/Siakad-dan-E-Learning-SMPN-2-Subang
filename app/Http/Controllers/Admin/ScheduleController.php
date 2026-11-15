<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScheduleRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);

        $query = Schedule::with(['classSubject.schoolClass', 'classSubject.subject', 'classSubject.teacher.user', 'classSubject.academicYear']);

        if ($selectedYearId) {
            $query->whereHas('classSubject', function ($q) use ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            });
        }

        if ($request->filled('class_id')) {
            $query->whereHas('classSubject', function ($q) use ($request) {
                $q->where('class_id', $request->input('class_id'));
            });
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->input('day_of_week'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $schedules = $query->orderByRaw("CASE day_of_week WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 WHEN 'saturday' THEN 6 ELSE 7 END")
            ->orderBy('start_time')
            ->paginate($perPage)
            ->withQueryString();

        $classes = SchoolClass::orderBy('grade_level')->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('admin.schedules.index', compact('schedules', 'classes', 'academicYears', 'selectedYearId'));
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

        ActivityLog::log(
            'create',
            'Schedule',
            $schedule->id,
            "Menambahkan jadwal {$schedule->classSubject->subject->name} kelas {$schedule->classSubject->schoolClass->name} hari {$schedule->day_of_week} ({$schedule->start_time}-{$schedule->end_time})."
        );

        return redirect()->route('admin.schedules.index', [
            'class_id' => $schedule->classSubject->class_id,
            'academic_year_id' => $schedule->classSubject->academic_year_id,
        ])->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        $schedule->load('classSubject');
        $classSubjects = ClassSubject::with(['schoolClass', 'subject', 'teacher.user'])
            ->where('academic_year_id', $schedule->classSubject->academic_year_id)
            ->get();

        return view('admin.schedules.edit', compact('schedule', 'classSubjects'));
    }

    public function update(ScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        ActivityLog::log(
            'update',
            'Schedule',
            $schedule->id,
            "Memperbarui jadwal {$schedule->classSubject->subject->name} kelas {$schedule->classSubject->schoolClass->name}."
        );

        return redirect()->route('admin.schedules.index', [
            'class_id' => $schedule->classSubject->class_id,
            'academic_year_id' => $schedule->classSubject->academic_year_id,
        ])->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $desc = "{$schedule->classSubject->subject->name} kelas {$schedule->classSubject->schoolClass->name}";
        $classId = $schedule->classSubject->class_id;
        $yearId = $schedule->classSubject->academic_year_id;
        $id = $schedule->id;

        $schedule->delete();

        ActivityLog::log('delete', 'Schedule', $id, "Menghapus jadwal {$desc}.");

        return redirect()->route('admin.schedules.index', ['class_id' => $classId, 'academic_year_id' => $yearId])
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
