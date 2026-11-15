<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScheduleGeneratorController extends Controller
{
    /**
     * Display the auto-generator configuration form.
     */
    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $classes = SchoolClass::withCount('classSubjects')->orderBy('grade_level')->orderBy('name')->get();

        $teachers = Teacher::with('user')
            ->withCount(['classSubjects' => function ($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                }
            }])
            ->get();

        // Calculate current scheduled hours per teacher
        $teacherStats = [];
        foreach ($teachers as $t) {
            $hours = Schedule::whereHas('classSubject', function ($q) use ($t, $activeYear) {
                $q->where('teacher_id', $t->id);
                if ($activeYear) {
                    $q->where('academic_year_id', $activeYear->id);
                }
            })->count() * 2; // Each slot is 2 JP (~80 mins)

            $teacherStats[] = [
                'id' => $t->id,
                'name' => $t->user->name,
                'nip' => $t->nip ?? '-',
                'max_hours' => $t->max_hours_per_week ?? 24,
                'current_hours' => $hours,
                'assigned_subjects_count' => $t->class_subjects_count,
            ];
        }

        return view('admin.schedules.auto-generate', compact(
            'activeYear',
            'academicYears',
            'classes',
            'teacherStats'
        ));
    }

    /**
     * Execute smart automatic scheduling.
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_ids' => ['nullable', 'array'],
            'class_ids.*' => ['exists:classes,id'],
            'clear_existing' => ['nullable', 'boolean'],
            'enforce_teacher_limits' => ['nullable', 'boolean'],
            'sessions_per_subject' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);

        $academicYearId = (int) $validated['academic_year_id'];
        $clearExisting = $request->boolean('clear_existing', false);
        $enforceLimits = $request->boolean('enforce_teacher_limits', true);
        $sessionsPerSubject = (int) ($validated['sessions_per_subject'] ?? 1);

        $classQuery = SchoolClass::query();
        if (! empty($validated['class_ids'])) {
            $classQuery->whereIn('id', $validated['class_ids']);
        }
        $targetClasses = $classQuery->orderBy('grade_level')->orderBy('name')->get();

        if ($targetClasses->isEmpty()) {
            return back()->with('error', 'Pilih minimal 1 kelas untuk dibuatkan jadwal otomatis.');
        }

        $createdSchedulesCount = 0;
        $skippedCount = 0;
        $warnings = [];

        DB::transaction(function () use (
            $targetClasses,
            $academicYearId,
            $clearExisting,
            $enforceLimits,
            $sessionsPerSubject,
            &$createdSchedulesCount,
            &$skippedCount,
            &$warnings
        ) {
            $classIds = $targetClasses->pluck('id')->toArray();

            if ($clearExisting) {
                Schedule::whereHas('classSubject', function ($q) use ($classIds, $academicYearId) {
                    $q->whereIn('class_id', $classIds)->where('academic_year_id', $academicYearId);
                })->delete();
            }

            // Track teacher bookings across the entire school: [day][start_time][teacher_id] = true
            $teacherBookings = [];
            // Track teacher total scheduled hours
            $teacherHours = [];

            // Load existing schedules for this year
            $existingSchedules = Schedule::with('classSubject')
                ->whereHas('classSubject', function ($q) use ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                })->get();

            foreach ($existingSchedules as $es) {
                $teacherId = $es->classSubject->teacher_id;
                $day = $es->day_of_week;
                $startTime = substr($es->start_time, 0, 5);

                $teacherBookings[$day][$startTime][$teacherId] = true;
                $teacherHours[$teacherId] = ($teacherHours[$teacherId] ?? 0) + 2;
            }

            // Days of operation (Senin s/d Jumat)
            $weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            foreach ($targetClasses as $schoolClass) {
                $classSubjects = ClassSubject::with(['subject', 'teacher'])
                    ->where('class_id', $schoolClass->id)
                    ->where('academic_year_id', $academicYearId)
                    ->get();

                if ($classSubjects->isEmpty()) {
                    continue;
                }

                // Generate grade-specific slot definitions
                $slotsByDay = $this->getTimeSlotsForGrade($schoolClass->grade_level);

                // Track slots occupied for this class: [day][slot_index] = true
                $classOccupied = [];
                // Track subjects assigned per day for this class: [day][subject_id] = true
                $classDaySubjects = [];

                // Mark pre-existing schedules for this class
                $currentClassSchedules = Schedule::whereHas('classSubject', function ($q) use ($schoolClass, $academicYearId) {
                    $q->where('class_id', $schoolClass->id)->where('academic_year_id', $academicYearId);
                })->get();

                foreach ($currentClassSchedules as $ccs) {
                    $day = $ccs->day_of_week;
                    $st = substr($ccs->start_time, 0, 5);
                    $classOccupied[$day][$st] = true;
                    $classDaySubjects[$day][$ccs->classSubject->subject_id] = true;
                }

                foreach ($classSubjects as $cs) {
                    $teacher = $cs->teacher;
                    $teacherId = $teacher?->id;
                    $teacherName = $teacher?->user?->name ?? 'Belum Ditentukan';
                    $maxHours = $teacher?->max_hours_per_week ?? 24;

                    for ($session = 0; $session < $sessionsPerSubject; $session++) {
                        // Check teacher load limit
                        if ($teacherId && $enforceLimits) {
                            $currentH = $teacherHours[$teacherId] ?? 0;
                            if ($currentH + 2 > $maxHours) {
                                $warnings[] = "Batas beban mengajar {$teacherName} ({$maxHours} Jam/Mgg) sudah tercapai. Mapel {$cs->subject->name} di Kelas {$schoolClass->name} tidak dapat dijadwalkan lagi.";
                                $skippedCount++;
                                break;
                            }
                        }

                        $scheduled = false;

                        // Find an available day and slot
                        foreach ($weekDays as $day) {
                            // Avoid placing the same subject twice on the same day for this class
                            if (isset($classDaySubjects[$day][$cs->subject_id])) {
                                continue;
                            }

                            $availableSlots = $slotsByDay[$day] ?? [];

                            foreach ($availableSlots as $slot) {
                                $st = $slot['start'];
                                $et = $slot['end'];

                                // 1. Check if class slot is free
                                if (isset($classOccupied[$day][$st])) {
                                    continue;
                                }

                                // 2. Check if teacher is free at this day and time across the school
                                if ($teacherId && isset($teacherBookings[$day][$st][$teacherId])) {
                                    continue;
                                }

                                // Slot is suitable! Create Schedule
                                Schedule::create([
                                    'class_subject_id' => $cs->id,
                                    'day_of_week' => $day,
                                    'start_time' => $st,
                                    'end_time' => $et,
                                    'room' => 'Ruang '.$schoolClass->name,
                                ]);

                                // Mark occupied
                                $classOccupied[$day][$st] = true;
                                $classDaySubjects[$day][$cs->subject_id] = true;
                                if ($teacherId) {
                                    $teacherBookings[$day][$st][$teacherId] = true;
                                    $teacherHours[$teacherId] = ($teacherHours[$teacherId] ?? 0) + 2;
                                }

                                $createdSchedulesCount++;
                                $scheduled = true;
                                break 2; // Move to next session/subject
                            }
                        }

                        if (! $scheduled) {
                            $skippedCount++;
                        }
                    }
                }
            }

            ActivityLog::log(
                'auto_generate_schedules',
                'schedules',
                null,
                "Berhasil membuat {$createdSchedulesCount} jadwal pelajaran otomatis untuk ".count($targetClasses)." kelas."
            );
        });

        $message = "Berhasil membuat {$createdSchedulesCount} jadwal pelajaran otomatis tanpa bentrok!";
        if (! empty($warnings)) {
            $message .= " (".count($warnings)." jadwal dibatasi oleh beban maksimal guru).";
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', $message)
            ->with('generator_warnings', array_slice($warnings, 0, 5));
    }

    /**
     * Define realistic time slots based on operator schedule settings.
     * All grade levels standard 07:00 - 14:00 WIB (Friday 11:15) with operator-configured recesses.
     */
    private function getTimeSlotsForGrade(string $gradeLevel): array
    {
        $timelineMonday = \App\Models\ScheduleSetting::getDayTimeline('monday');
        $timelineFriday = \App\Models\ScheduleSetting::getDayTimeline('friday');

        $seninKamis = [];
        foreach ($timelineMonday as $item) {
            if ($item['type'] === 'period') {
                $seninKamis[] = [
                    'start' => $item['start_time'],
                    'end' => $item['end_time'],
                    'label' => $item['name'],
                ];
            }
        }

        $jumat = [];
        foreach ($timelineFriday as $item) {
            if ($item['type'] === 'period') {
                $jumat[] = [
                    'start' => $item['start_time'],
                    'end' => $item['end_time'],
                    'label' => $item['name'],
                ];
            }
        }

        return [
            'monday' => $seninKamis,
            'tuesday' => $seninKamis,
            'wednesday' => $seninKamis,
            'thursday' => $seninKamis,
            'friday' => $jumat,
        ];
    }
}
