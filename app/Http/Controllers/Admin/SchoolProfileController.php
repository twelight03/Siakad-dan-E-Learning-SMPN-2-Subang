<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolProfileController extends Controller
{
    /**
     * Display the school profile & Dapodik data configuration form.
     */
    public function index(): View
    {
        $dapodik = SchoolSetting::getDapodikStats();

        return view('admin.school-profile.index', compact('dapodik'));
    }

    /**
     * Update school profile & Dapodik data settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dapodik_mode' => ['required', 'in:manual,auto'],
            'dapodik_students' => ['nullable', 'integer', 'min:0'],
            'dapodik_teachers' => ['nullable', 'integer', 'min:0'],
            'dapodik_classes' => ['nullable', 'integer', 'min:0'],
            'dapodik_subjects' => ['nullable', 'integer', 'min:0'],
            'dapodik_students_label' => ['nullable', 'string', 'max:100'],
            'dapodik_teachers_label' => ['nullable', 'string', 'max:100'],
            'dapodik_classes_label' => ['nullable', 'string', 'max:100'],
            'dapodik_subjects_label' => ['nullable', 'string', 'max:100'],
            'school_npsn' => ['nullable', 'string', 'max:20'],
            'school_accreditation' => ['nullable', 'string', 'max:50'],
            'school_address' => ['nullable', 'string', 'max:255'],
            'school_phone' => ['nullable', 'string', 'max:50'],
            'school_email' => ['nullable', 'email', 'max:100'],
            'principal_name' => ['nullable', 'string', 'max:100'],
            'principal_speech' => ['nullable', 'string', 'max:1000'],
        ]);

        SchoolSetting::set('dapodik_mode', $validated['dapodik_mode']);

        if (isset($validated['dapodik_students'])) {
            SchoolSetting::set('dapodik_students', (int) $validated['dapodik_students']);
        }
        if (isset($validated['dapodik_teachers'])) {
            SchoolSetting::set('dapodik_teachers', (int) $validated['dapodik_teachers']);
        }
        if (isset($validated['dapodik_classes'])) {
            SchoolSetting::set('dapodik_classes', (int) $validated['dapodik_classes']);
        }
        if (isset($validated['dapodik_subjects'])) {
            SchoolSetting::set('dapodik_subjects', (int) $validated['dapodik_subjects']);
        }

        if (isset($validated['dapodik_students_label'])) {
            SchoolSetting::set('dapodik_students_label', $validated['dapodik_students_label']);
        }
        if (isset($validated['dapodik_teachers_label'])) {
            SchoolSetting::set('dapodik_teachers_label', $validated['dapodik_teachers_label']);
        }
        if (isset($validated['dapodik_classes_label'])) {
            SchoolSetting::set('dapodik_classes_label', $validated['dapodik_classes_label']);
        }
        if (isset($validated['dapodik_subjects_label'])) {
            SchoolSetting::set('dapodik_subjects_label', $validated['dapodik_subjects_label']);
        }

        if (isset($validated['school_npsn'])) {
            SchoolSetting::set('school_npsn', $validated['school_npsn']);
        }
        if (isset($validated['school_accreditation'])) {
            SchoolSetting::set('school_accreditation', $validated['school_accreditation']);
        }
        if (isset($validated['school_address'])) {
            SchoolSetting::set('school_address', $validated['school_address']);
        }
        if (isset($validated['school_phone'])) {
            SchoolSetting::set('school_phone', $validated['school_phone']);
        }
        if (isset($validated['school_email'])) {
            SchoolSetting::set('school_email', $validated['school_email']);
        }
        if (isset($validated['principal_name'])) {
            SchoolSetting::set('principal_name', $validated['principal_name']);
        }
        if (isset($validated['principal_speech'])) {
            SchoolSetting::set('principal_speech', $validated['principal_speech']);
        }

        ActivityLog::log('update', 'SchoolSetting', null, 'Memperbarui data pokok Dapodik dan profil sekolah.');
        SchoolSetting::clearCache();

        return redirect()->route('admin.school-profile.index')
            ->with('success', 'Data pokok Dapodik dan identitas profil sekolah berhasil diperbarui!');
    }
}
