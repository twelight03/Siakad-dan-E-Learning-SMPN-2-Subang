<?php

namespace App\Http\Requests\Teacher;

use App\Models\ClassSubject;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->user()?->teacher;
        if (! $teacher) {
            return false;
        }

        $classSubjectId = $this->input('class_subject_id');
        if ($classSubjectId) {
            return ClassSubject::where('id', $classSubjectId)
                ->where('teacher_id', $teacher->id)
                ->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'class_subject_id' => ['required', 'exists:class_subjects,id'],
            'session_date' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.status' => ['required', 'in:present,excused,sick,absent'],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Mata pelajaran dan kelas wajib dipilih.',
            'session_date.required' => 'Tanggal sesi absensi wajib diisi.',
            'session_date.date' => 'Format tanggal tidak valid.',
            'attendances.required' => 'Daftar kehadiran siswa wajib diisi.',
            'attendances.*.status.required' => 'Status kehadiran setiap siswa wajib ditentukan.',
            'attendances.*.status.in' => 'Status kehadiran harus berupa hadir, izin, sakit, atau alpa.',
        ];
    }
}
