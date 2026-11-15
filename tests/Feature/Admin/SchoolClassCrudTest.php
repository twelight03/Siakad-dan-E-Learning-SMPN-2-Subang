<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_classes_list(): void
    {
        $admin = User::factory()->admin()->create();
        SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);

        $response = $this->actingAs($admin)->get('/admin/classes');

        $response->assertStatus(200);
        $response->assertSee('VII-A');
        $response->assertSee('Kelas 7');
    }

    public function test_non_admin_cannot_access_classes(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/classes');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_class(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '1234567']);

        $response = $this->actingAs($admin)->post('/admin/classes', [
            'name' => 'VIII-B',
            'grade_level' => '8',
            'homeroom_teacher_id' => $teacher->id,
        ]);

        $response->assertRedirect('/admin/classes');
        $this->assertDatabaseHas('classes', [
            'name' => 'VIII-B',
            'grade_level' => '8',
            'homeroom_teacher_id' => $teacher->id,
        ]);
    }

    public function test_class_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        SchoolClass::create(['name' => 'IX-A', 'grade_level' => '9']);

        $response = $this->actingAs($admin)->post('/admin/classes', [
            'name' => 'IX-A', // duplicate
            'grade_level' => '9',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_class(): void
    {
        $admin = User::factory()->admin()->create();
        $class = SchoolClass::create(['name' => 'VII-D', 'grade_level' => '7']);

        $response = $this->actingAs($admin)->put("/admin/classes/{$class->id}", [
            'name' => 'VII-E',
            'grade_level' => '7',
        ]);

        $response->assertRedirect('/admin/classes');
        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'name' => 'VII-E',
        ]);
    }

    public function test_admin_can_delete_unreferenced_class(): void
    {
        $admin = User::factory()->admin()->create();
        $class = SchoolClass::create(['name' => 'IX-Z', 'grade_level' => '9']);

        $response = $this->actingAs($admin)->delete("/admin/classes/{$class->id}");

        $response->assertRedirect('/admin/classes');
        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }

    public function test_cannot_delete_class_with_enrollments(): void
    {
        $admin = User::factory()->admin()->create();
        $class = SchoolClass::create(['name' => 'VII-Active', 'grade_level' => '7']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '445566', 'gender' => 'female']);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/classes/{$class->id}");

        $response->assertRedirect('/admin/classes');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('classes', ['id' => $class->id]);
    }
}
