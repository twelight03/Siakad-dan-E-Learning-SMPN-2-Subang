<?php

namespace App\Http\Requests\Admin;

use App\Models\ClassSubject;
use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Penugasan kelas dan mata pelajaran wajib dipilih.',
            'day_of_week.required' => 'Hari jadwal pelajaran wajib dipilih.',
            'day_of_week.in' => 'Hari tidak valid.',
            'start_time.required' => 'Jam mulai pelajaran wajib diisi.',
            'start_time.date_format' => 'Format jam mulai harus JJ:MM (contoh: 07:30).',
            'end_time.required' => 'Jam selesai pelajaran wajib diisi.',
            'end_time.date_format' => 'Format jam selesai harus JJ:MM (contoh: 09:00).',
            'end_time.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $scheduleId = $this->route('schedule')?->id;
            $classSubject = ClassSubject::find($this->input('class_subject_id'));
            if (! $classSubject) {
                return;
            }

            $day = $this->input('day_of_week');
            $startTime = $this->input('start_time');
            $endTime = $this->input('end_time');

            // 1. Check Class Schedule Conflict (Kelas yang sama di jam yang bentrok)
            $classConflict = Schedule::where('id', '!=', $scheduleId)
                ->where('day_of_week', $day)
                ->whereHas('classSubject', function ($q) use ($classSubject) {
                    $q->where('class_id', $classSubject->class_id)
                      ->where('academic_year_id', $classSubject->academic_year_id);
                })
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                })
                ->first();

            if ($classConflict) {
                $validator->errors()->add(
                    'start_time',
                    "Jadwal bentrok: Kelas {$classSubject->schoolClass->name} sudah memiliki jadwal lain pada hari ini di jam tersebut ({$classConflict->classSubject->subject->name}: {$classConflict->start_time} - {$classConflict->end_time})."
                );
            }

            // 2. Check Teacher Schedule Conflict (Guru yang sama mengajar di kelas lain pada jam bentrok)
            $teacherConflict = Schedule::where('id', '!=', $scheduleId)
                ->where('day_of_week', $day)
                ->whereHas('classSubject', function ($q) use ($classSubject) {
                    $q->where('teacher_id', $classSubject->teacher_id)
                      ->where('academic_year_id', $classSubject->academic_year_id);
                })
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                })
                ->first();

            if ($teacherConflict) {
                $validator->errors()->add(
                    'start_time',
                    "Jadwal bentrok: Guru {$classSubject->teacher->user->name} sudah dijadwalkan mengajar di kelas {$teacherConflict->classSubject->schoolClass->name} pada jam tersebut."
                );
            }
        });
    }
}
