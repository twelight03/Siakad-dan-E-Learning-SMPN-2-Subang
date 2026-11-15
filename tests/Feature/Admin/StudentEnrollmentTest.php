<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_enrollments_list(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $studentUser = User::factory()->student()->create(['name' => 'Anisa Rahma']);
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '101010', 'gender' => 'female']);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/enrollments');

        $response->assertStatus(200);
        $response->assertSee('Anisa Rahma');
        $response->assertSee('101010');
        $response->assertSee('VII-A');
    }

    public function test_non_admin_cannot_access_enrollments(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/admin/enrollments');

        $response->assertStatus(403);
    }

    public function test_admin_can_enroll_student_to_class(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-B', 'grade_level' => '7']);
        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '202020', 'gender' => 'male']);

        $response = $this->actingAs($admin)->post('/admin/enrollments', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);
    }

    public function test_cannot_enroll_student_twice_in_same_academic_year(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $classA = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $classB = SchoolClass::create(['name' => 'VII-B', 'grade_level' => '7']);

        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '303030', 'gender' => 'male']);

        // First enrollment
        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $classA->id,
            'academic_year_id' => $year->id,
        ]);

        // Attempt second enrollment in classB for same year
        $response = $this->actingAs($admin)->post('/admin/enrollments', [
            'student_id' => $student->id,
            'class_id' => $classB->id,
            'academic_year_id' => $year->id,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertEquals(1, StudentEnrollment::where('student_id', $student->id)->where('academic_year_id', $year->id)->count());
    }

    public function test_admin_can_transfer_student_to_different_class(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $classOld = SchoolClass::create(['name' => 'VII-Old', 'grade_level' => '7']);
        $classNew = SchoolClass::create(['name' => 'VII-New', 'grade_level' => '7']);

        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '404040', 'gender' => 'male']);

        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $classOld->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->put("/admin/enrollments/{$enrollment->id}", [
            'student_id' => $student->id,
            'class_id' => $classNew->id,
            'academic_year_id' => $year->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_enrollments', [
            'id' => $enrollment->id,
            'class_id' => $classNew->id,
        ]);
    }

    public function test_admin_can_delete_enrollment(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-Del', 'grade_level' => '7']);
        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '505050', 'gender' => 'male']);

        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/enrollments/{$enrollment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('student_enrollments', ['id' => $enrollment->id]);
    }
}
