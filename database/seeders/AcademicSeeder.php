<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentGrade;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\Material;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherBudi = Teacher::where('nip', '198501012010011001')->first();
        $teacherSiti = Teacher::where('nip', '198802022012022002')->first();

        // 1. Academic Year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027', 'semester' => 'odd'],
            [
                'start_date' => '2026-07-15',
                'end_date' => '2026-12-20',
                'is_active' => true,
            ]
        );

        // 2. Classes
        $class7A = SchoolClass::firstOrCreate(
            ['name' => 'VII-A'],
            [
                'grade_level' => '7',
                'homeroom_teacher_id' => $teacherBudi?->id,
            ]
        );

        $class7B = SchoolClass::firstOrCreate(
            ['name' => 'VII-B'],
            [
                'grade_level' => '7',
                'homeroom_teacher_id' => $teacherSiti?->id,
            ]
        );

        $class8A = SchoolClass::firstOrCreate(
            ['name' => 'VIII-A'],
            [
                'grade_level' => '8',
                'homeroom_teacher_id' => null,
            ]
        );

        // 3. Subjects
        $subMath = Subject::firstOrCreate(
            ['code' => 'MAT-7'],
            ['name' => 'Matematika Kelas VII', 'description' => 'Mata pelajaran matematika dasar kelas 7']
        );

        $subIpa = Subject::firstOrCreate(
            ['code' => 'IPA-7'],
            ['name' => 'Ilmu Pengetahuan Alam Kelas VII', 'description' => 'Mata pelajaran IPA terpadu kelas 7']
        );

        $subIndo = Subject::firstOrCreate(
            ['code' => 'BIN-7'],
            ['name' => 'Bahasa Indonesia Kelas VII', 'description' => 'Mata pelajaran Bahasa Indonesia kelas 7']
        );

        // 4. Enrollments
        $studentAhmad = Student::where('nis', '202607001')->first();
        $studentDewi = Student::where('nis', '202607002')->first();
        $studentRizky = Student::where('nis', '202607003')->first();

        if ($studentAhmad && $class7A) {
            StudentEnrollment::firstOrCreate([
                'student_id' => $studentAhmad->id,
                'academic_year_id' => $academicYear->id,
            ], [
                'class_id' => $class7A->id,
            ]);
        }

        if ($studentDewi && $class7A) {
            StudentEnrollment::firstOrCreate([
                'student_id' => $studentDewi->id,
                'academic_year_id' => $academicYear->id,
            ], [
                'class_id' => $class7A->id,
            ]);
        }

        if ($studentRizky && $class7B) {
            StudentEnrollment::firstOrCreate([
                'student_id' => $studentRizky->id,
                'academic_year_id' => $academicYear->id,
            ], [
                'class_id' => $class7B->id,
            ]);
        }

        // 5. Class Subjects
        $csMath7A = ClassSubject::firstOrCreate([
            'class_id' => $class7A->id,
            'subject_id' => $subMath->id,
            'academic_year_id' => $academicYear->id,
        ], [
            'teacher_id' => $teacherBudi?->id,
        ]);

        $csIpa7A = ClassSubject::firstOrCreate([
            'class_id' => $class7A->id,
            'subject_id' => $subIpa->id,
            'academic_year_id' => $academicYear->id,
        ], [
            'teacher_id' => $teacherSiti?->id,
        ]);

        $csMath7B = ClassSubject::firstOrCreate([
            'class_id' => $class7B->id,
            'subject_id' => $subMath->id,
            'academic_year_id' => $academicYear->id,
        ], [
            'teacher_id' => $teacherBudi?->id,
        ]);

        // 6. Schedules
        Schedule::firstOrCreate([
            'class_subject_id' => $csMath7A->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
        ], [
            'room' => 'Ruang 7-A',
        ]);

        Schedule::firstOrCreate([
            'class_subject_id' => $csIpa7A->id,
            'day_of_week' => 'wednesday',
            'start_time' => '09:15:00',
            'end_time' => '10:45:00',
        ], [
            'room' => 'Lab IPA',
        ]);

        Schedule::firstOrCreate([
            'class_subject_id' => $csMath7B->id,
            'day_of_week' => 'tuesday',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
        ], [
            'room' => 'Ruang 7-B',
        ]);

        // 7. Grade Components
        $gcTugas = GradeComponent::firstOrCreate([
            'class_subject_id' => $csMath7A->id,
            'name' => 'Tugas',
        ], [
            'weight' => 20.00,
            'max_score' => 100.00,
        ]);

        $gcUts = GradeComponent::firstOrCreate([
            'class_subject_id' => $csMath7A->id,
            'name' => 'UTS',
        ], [
            'weight' => 30.00,
            'max_score' => 100.00,
        ]);

        $gcUas = GradeComponent::firstOrCreate([
            'class_subject_id' => $csMath7A->id,
            'name' => 'UAS',
        ], [
            'weight' => 50.00,
            'max_score' => 100.00,
        ]);

        // 8. Sample Grades
        if ($studentAhmad && $teacherBudi) {
            Grade::firstOrCreate([
                'grade_component_id' => $gcTugas->id,
                'student_id' => $studentAhmad->id,
            ], [
                'score' => 88.50,
                'feedback' => 'Pengerjaan sangat rapi dan teliti.',
                'graded_by' => $teacherBudi->user_id,
            ]);

            Grade::firstOrCreate([
                'grade_component_id' => $gcUts->id,
                'student_id' => $studentAhmad->id,
            ], [
                'score' => 85.00,
                'feedback' => 'Hasil baik.',
                'graded_by' => $teacherBudi->user_id,
            ]);
        }

        // 9. Attendance Session & Attendances
        if ($teacherBudi) {
            $attSession = AttendanceSession::firstOrCreate([
                'class_subject_id' => $csMath7A->id,
                'session_date' => '2026-08-03',
            ], [
                'topic' => 'Pengenalan Aljabar',
                'created_by' => $teacherBudi->user_id,
            ]);

            if ($studentAhmad) {
                Attendance::firstOrCreate([
                    'attendance_session_id' => $attSession->id,
                    'student_id' => $studentAhmad->id,
                ], [
                    'status' => 'present',
                    'note' => 'Hadir tepat waktu',
                    'recorded_by' => $teacherBudi->user_id,
                ]);
            }

            if ($studentDewi) {
                Attendance::firstOrCreate([
                    'attendance_session_id' => $attSession->id,
                    'student_id' => $studentDewi->id,
                ], [
                    'status' => 'present',
                    'note' => 'Hadir tepat waktu',
                    'recorded_by' => $teacherBudi->user_id,
                ]);
            }

            // 10. Material
            Material::firstOrCreate([
                'class_subject_id' => $csMath7A->id,
                'title' => 'Modul 1: Konsep Dasar Aljabar',
            ], [
                'description' => 'Materi pengantar aljabar untuk siswa kelas 7 SMP.',
                'file_path' => 'materials/modul-1-aljabar.pdf',
                'original_filename' => 'modul-1-aljabar.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1048576,
                'uploaded_by' => $teacherBudi->user_id,
            ]);

            // 11. Assignment & Submission
            $assignment = Assignment::firstOrCreate([
                'class_subject_id' => $csMath7A->id,
                'title' => 'Latihan Soal Aljabar 1',
            ], [
                'description' => 'Kerjakan soal latihan nomor 1 sampai 10 di buku catatan atau ketik dalam format PDF.',
                'attachment_path' => null,
                'deadline' => now()->addDays(7),
                'created_by' => $teacherBudi->user_id,
            ]);

            if ($studentAhmad) {
                $sub = Submission::firstOrCreate([
                    'assignment_id' => $assignment->id,
                    'student_id' => $studentAhmad->id,
                ], [
                    'file_path' => null,
                    'text_answer' => 'Jawaban Soal 1: 5x + 3 = 18 => 5x = 15 => x = 3.',
                    'submitted_at' => now()->subDay(),
                    'status' => 'submitted',
                ]);

                AssignmentGrade::firstOrCreate([
                    'submission_id' => $sub->id,
                ], [
                    'score' => 90.00,
                    'feedback' => 'Bagus sekali langkah penyelesaiannya!',
                    'graded_by' => $teacherBudi->user_id,
                    'graded_at' => now(),
                ]);
            }
        }
    }
}
