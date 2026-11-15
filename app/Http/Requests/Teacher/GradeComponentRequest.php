<?php

namespace App\Http\Requests\Teacher;

use App\Models\ClassSubject;
use App\Models\GradeComponent;
use Illuminate\Foundation\Http\FormRequest;

class GradeComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $teacher = $this->user()?->teacher;
        if (! $teacher) {
            return false;
        }

        $classSubjectId = $this->input('class_subject_id') ?? $this->route('grade_component')?->class_subject_id;
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
            'class_subject_id' => $this->isMethod('POST') ? ['required', 'exists:class_subjects,id'] : ['sometimes', 'exists:class_subjects,id'],
            'name' => ['required', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_score' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $classSubjectId = $this->input('class_subject_id') ?? $this->route('grade_component')?->class_subject_id;
            $newWeight = (float) $this->input('weight');
            $componentId = $this->route('grade_component')?->id;

            if ($classSubjectId && $newWeight) {
                $query = GradeComponent::where('class_subject_id', $classSubjectId);
                if ($componentId) {
                    $query->where('id', '!=', $componentId);
                }
                $existingSum = (float) $query->sum('weight');
                if (($existingSum + $newWeight) > 100.0) {
                    $available = 100.0 - $existingSum;
                    $validator->errors()->add(
                        'weight',
                        "Total bobot komponen tidak boleh melebihi 100%. Bobot yang tersedia saat ini: {$available}%."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'class_subject_id.required' => 'Mata pelajaran dan kelas wajib dipilih.',
            'name.required' => 'Nama komponen nilai wajib diisi.',
            'name.max' => 'Nama komponen maksimal 100 karakter.',
            'weight.required' => 'Bobot komponen nilai wajib diisi.',
            'weight.numeric' => 'Bobot harus berupa angka.',
            'weight.min' => 'Bobot minimal 1%.',
            'weight.max' => 'Bobot maksimal 100%.',
        ];
    }
}
