<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Clear all cached school settings and landing page stats.
     */
    public static function clearCache(): void
    {
        Cache::forget('siakad_dapodik_stats');
        Cache::forget('siakad_landing_page_stats');
    }

    /**
     * Get a setting value with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();
            if (! $setting) {
                return $default;
            }

            $decoded = json_decode($setting->value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set / update a setting value by key and flush cache.
     */
    public static function set(string $key, mixed $value): self
    {
        $val = is_array($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $val]
        );

        static::clearCache();

        return $setting;
    }

    /**
     * Get full Dapodik stats & school profile with 1-hour cache layer (Anti-DDoS Resource Protection).
     */
    public static function getDapodikStats(): array
    {
        return Cache::remember('siakad_dapodik_stats', 3600, function () {
            $mode = static::get('dapodik_mode', 'manual'); // 'manual' or 'auto'

            // Default official census figures for SMP Negeri 2 Subang
            $manualStudents = (int) static::get('dapodik_students', 648);
            $manualTeachers = (int) static::get('dapodik_teachers', 38);
            $manualClasses = (int) static::get('dapodik_classes', 18);
            $manualSubjects = (int) static::get('dapodik_subjects', 11);

            $studentsLabel = static::get('dapodik_students_label', 'Siswa aktif terdaftar');
            $teachersLabel = static::get('dapodik_teachers_label', 'Guru pengampu bersertifikasi');
            $classesLabel = static::get('dapodik_classes_label', 'Tingkat VII, VIII, dan IX');
            $subjectsLabel = static::get('dapodik_subjects_label', 'Kurikulum Nasional');

            // Live database counts
            $dbStudents = 0;
            $dbTeachers = 0;
            $dbClasses = 0;
            $dbSubjects = 0;

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                    $dbStudents = Student::count();
                    $dbTeachers = Teacher::count();
                    $dbClasses = SchoolClass::count();
                    $dbSubjects = Subject::count();
                }
            } catch (\Throwable $e) {
                // Ignore if DB not ready
            }

            $useAuto = ($mode === 'auto');

            return [
                'mode' => $mode,
                'total_students' => $useAuto ? $dbStudents : $manualStudents,
                'total_teachers' => $useAuto ? $dbTeachers : $manualTeachers,
                'total_classes' => $useAuto ? $dbClasses : $manualClasses,
                'total_subjects' => $useAuto ? $dbSubjects : $manualSubjects,

                'manual_students' => $manualStudents,
                'manual_teachers' => $manualTeachers,
                'manual_classes' => $manualClasses,
                'manual_subjects' => $manualSubjects,

                'db_students' => $dbStudents,
                'db_teachers' => $dbTeachers,
                'db_classes' => $dbClasses,
                'db_subjects' => $dbSubjects,

                'students_label' => $studentsLabel,
                'teachers_label' => $teachersLabel,
                'classes_label' => $classesLabel,
                'subjects_label' => $subjectsLabel,

                // Institutional Identity
                'npsn' => static::get('school_npsn', '20212952'),
                'accreditation' => static::get('school_accreditation', 'A (Unggul)'),
                'school_address' => static::get('school_address', 'Jl. Raya Pamulihan - Tangkolo, Kec. Subang, Kab. Kuningan, Jawa Barat 45566'),
                'school_phone' => static::get('school_phone', '(0232) 8892-SMPN2'),
                'school_email' => static::get('school_email', 'tu@smpn2subang.sch.id'),
                'principal_name' => static::get('principal_name', 'Kepala Sekolah'),
                'principal_speech' => static::get('principal_speech', 'Penerapan Sistem Informasi Akademik (SIAKAD) ini merupakan langkah strategis SMP Negeri 2 Subang dalam mendigitalisasi layanan pendidikan. Melalui platform ini, kami memastikan keterbukaan informasi capaian belajar siswa, ketertiban administrasi mengajar dewan guru, serta kemudahan orang tua dalam mendampingi perkembangan putra-putrinya menuju insan yang cerdas dan berakhlak mulia.'),
            ];
        });
    }
}
