<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assignment;
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
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_correct_statistics_and_logs(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Pak Admin']);
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $teacherUser = User::factory()->teacher()->create();
        Teacher::create(['user_id' => $teacherUser->id, 'nip' => '123456789']);

        $studentUser = User::factory()->student()->create();
        Student::create(['user_id' => $studentUser->id, 'nis' => '987654321', 'gender' => 'male']);

        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        Subject::create(['code' => 'MAT-7', 'name' => 'Matematika']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Total Siswa');
        $response->assertSee('Total Guru');
        $response->assertSee('2026/2027');
        $response->assertSee('Ganjil');
    }

    public function test_teacher_dashboard_displays_assigned_classes_and_schedules(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-11-16 08:00:00'); // Monday
        $teacherUser = User::factory()->teacher()->create(['name' => 'Ibu Guru']);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '1122334455']);

        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class = SchoolClass::create(['name' => 'VII-B', 'grade_level' => '7']);
        $subject = Subject::create(['code' => 'IPA-7', 'name' => 'Ilmu Pengetahuan Alam']);

        $classSubject = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $dayOfWeek = strtolower(now()->englishDayOfWeek);
        Schedule::create([
            'class_subject_id' => $classSubject->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
            'room' => 'Lab IPA',
        ]);

        $response = $this->actingAs($teacherUser)->get('/teacher/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Guru');
        $response->assertSee('VII-B');
        $response->assertSee('Ilmu Pengetahuan Alam');
        $response->assertSee('Lab IPA');
    }

    public function test_student_dashboard_displays_enrollment_and_academics(): void
    {
        $studentUser = User::factory()->student()->create(['name' => 'Siswa Pintar']);
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '112233', 'gender' => 'male']);

        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class = SchoolClass::create(['name' => 'VII-C', 'grade_level' => '7']);
        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $subject = Subject::create(['code' => 'BIN-7', 'name' => 'Bahasa Indonesia']);
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '998877']);

        $classSubject = ClassSubject::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $component = GradeComponent::create([
            'class_subject_id' => $classSubject->id,
            'name' => 'Tugas 1',
            'weight' => 20,
            'max_score' => 100,
        ]);

        Grade::create([
            'grade_component_id' => $component->id,
            'student_id' => $student->id,
            'score' => 95.0,
            'feedback' => 'Sangat memuaskan',
            'graded_by' => $teacherUser->id,
        ]);

        $response = $this->actingAs($studentUser)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Siswa');
        $response->assertSee('VII-C');
        $response->assertSee('Bahasa Indonesia');
        $response->assertSee('95.0');
        $response->assertSee('Sangat memuaskan');
    }
}
