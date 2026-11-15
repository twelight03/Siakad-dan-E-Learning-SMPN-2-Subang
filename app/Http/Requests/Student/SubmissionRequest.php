<?php

namespace App\Http\Requests\Student;

use App\Models\Assignment;
use App\Models\StudentEnrollment;
use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->user()?->student;
        if (! $student) {
            return false;
        }

        $assignment = $this->route('assignment');
        if ($assignment) {
            return StudentEnrollment::where('student_id', $student->id)
                ->where('class_id', $assignment->classSubject->class_id)
                ->where('academic_year_id', $assignment->classSubject->academic_year_id)
                ->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'submission_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png', 'max:10240'],
            'text_answer' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->hasFile('submission_file') && empty(trim((string) $this->input('text_answer')))) {
                $validator->errors()->add(
                    'submission_file',
                    'Harap unggah berkas jawaban atau isi jawaban tertulis.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'submission_file.file' => 'Berkas yang diunggah tidak valid.',
            'submission_file.mimes' => 'Format berkas harus berupa: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, atau PNG.',
            'submission_file.max' => 'Ukuran berkas jawaban maksimal 10 MB.',
            'text_answer.max' => 'Jawaban tertulis maksimal 5.000 karakter.',
        ];
    }
}
