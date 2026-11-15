<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcademicYearRequest;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(Request $request): View
    {
        $query = AcademicYear::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $academicYears = $query->orderBy('start_date', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create(): View
    {
        return view('admin.academic-years.create');
    }

    public function store(AcademicYearRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $isActive = $request->boolean('is_active');
        $data['is_active'] = $isActive;

        DB::transaction(function () use ($data, $isActive) {
            if ($isActive) {
                AcademicYear::query()->update(['is_active' => false]);
            }
            $year = AcademicYear::create($data);
            ActivityLog::log('create', 'AcademicYear', $year->id, "Menambahkan tahun ajaran {$year->name} ({$year->semester}).");
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validated();
        $isActive = $request->boolean('is_active');
        $data['is_active'] = $isActive;

        DB::transaction(function () use ($academicYear, $data, $isActive) {
            if ($isActive) {
                AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }
            $academicYear->update($data);
            ActivityLog::log('update', 'AcademicYear', $academicYear->id, "Memperbarui tahun ajaran {$academicYear->name}.");
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->enrollments()->exists() || $academicYear->classSubjects()->exists()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Tahun ajaran tidak dapat dihapus karena sudah memiliki data siswa terdaftar atau penugasan mapel.');
        }

        $name = $academicYear->name;
        $id = $academicYear->id;
        $academicYear->delete();

        ActivityLog::log('delete', 'AcademicYear', $id, "Menghapus tahun ajaran {$name}.");

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
