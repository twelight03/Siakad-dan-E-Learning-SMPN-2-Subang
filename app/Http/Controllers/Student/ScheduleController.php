<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $student = auth()->user()->student;
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $currentEnrollment = $student->currentEnrollment;
        $schedules = collect();

        if ($currentEnrollment) {
            $schedules = Schedule::with(['classSubject.subject', 'classSubject.teacher.user', 'schoolClass'])
                ->where('class_id', $currentEnrollment->class_id)
                ->whereHas('classSubject', function ($q) use ($currentEnrollment) {
                    $q->where('academic_year_id', $currentEnrollment->academic_year_id);
                })
                ->orderByRaw("CASE day_of_week 
                    WHEN 'monday' THEN 1 
                    WHEN 'tuesday' THEN 2 
                    WHEN 'wednesday' THEN 3 
                    WHEN 'thursday' THEN 4 
                    WHEN 'friday' THEN 5 
                    WHEN 'saturday' THEN 6 
                    ELSE 7 END")
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
        }

        return view('student.schedules.index', compact('schedules', 'currentEnrollment'));
    }
}
