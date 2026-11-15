<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    public function index(Request $request): View
    {
        $student = auth()->user()->student;
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $currentEnrollment = $student->currentEnrollment;
        $materials = collect();

        if ($currentEnrollment) {
            $query = Material::with(['classSubject.subject', 'classSubject.schoolClass', 'uploader'])
                ->whereHas('classSubject', function ($q) use ($currentEnrollment) {
                    $q->where('class_id', $currentEnrollment->class_id)
                      ->where('academic_year_id', $currentEnrollment->academic_year_id);
                });

            if ($request->filled('subject_id')) {
                $query->whereHas('classSubject', fn ($q) => $q->where('subject_id', $request->input('subject_id')));
            }

            $perPage = (int) $request->input('per_page', 10);
            if (! in_array($perPage, [10, 25, 50, 100])) {
                $perPage = 10;
            }

            $materials = $query->latest('id')->paginate($perPage)->withQueryString();
        }

        return view('student.materials.index', compact('materials', 'currentEnrollment', 'student'));
    }

    public function download(Material $material): StreamedResponse
    {
        $student = auth()->user()->student;
        abort_if(! $student, 403, 'Akses khusus siswa.');

        $isEnrolled = StudentEnrollment::where('student_id', $student->id)
            ->where('class_id', $material->classSubject->class_id)
            ->where('academic_year_id', $material->classSubject->academic_year_id)
            ->exists();

        abort_if(! $isEnrolled, 403, 'Anda tidak memiliki akses ke materi kelas ini.');
        abort_if(! Storage::disk('local')->exists($material->file_path), 404, 'Berkas materi tidak ditemukan.');

        return Storage::disk('local')->download($material->file_path, $material->original_filename);
    }
}
