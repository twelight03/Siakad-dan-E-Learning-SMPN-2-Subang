<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $enrollmentId = $this->route('enrollment')?->id;
        $studentId = $this->input('student_id');
        $academicYearId = $this->input('academic_year_id');

        return [
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('student_enrollments')
                    ->where('academic_year_id', $academicYearId)
                    ->ignore($enrollmentId),
            ],
            'class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'student_id.exists' => 'Siswa yang dipilih tidak valid.',
            'student_id.unique' => 'Siswa ini sudah terdaftar di kelas lain pada tahun ajaran yang dipilih.',
            'class_id.required' => 'Rombel / Kelas wajib dipilih.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
            'academic_year_id.exists' => 'Tahun ajaran yang dipilih tidak valid.',
        ];
    }
}
