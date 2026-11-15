<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $classSubjectId = $this->route('class_subject')?->id;
        $classId = $this->input('class_id');
        $subjectId = $this->input('subject_id');
        $academicYearId = $this->input('academic_year_id');

        return [
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('class_subjects')
                    ->where('class_id', $classId)
                    ->where('academic_year_id', $academicYearId)
                    ->ignore($classSubjectId),
            ],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Rombel / Kelas wajib dipilih.',
            'subject_id.required' => 'Mata pelajaran wajib dipilih.',
            'subject_id.unique' => 'Mata pelajaran ini sudah ditugaskan ke guru lain pada kelas dan tahun ajaran yang sama.',
            'teacher_id.required' => 'Guru pengampu wajib dipilih.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
        ];
    }
}
