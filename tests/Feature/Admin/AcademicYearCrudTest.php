<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_academic_years_list(): void
    {
        $admin = User::factory()->admin()->create();
        AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/academic-years');

        $response->assertStatus(200);
        $response->assertSee('2026/2027');
        $response->assertSee('Ganjil');
    }

    public function test_non_admin_cannot_access_academic_years(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/admin/academic-years');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_academic_year_with_valid_data(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/academic-years', [
            'name' => '2027/2028',
            'semester' => 'even',
            'start_date' => '2028-01-05',
            'end_date' => '2028-06-25',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/academic-years');
        $this->assertDatabaseHas('academic_years', [
            'name' => '2027/2028',
            'semester' => 'even',
            'is_active' => true,
        ]);
    }

    public function test_academic_year_creation_fails_when_end_date_before_start_date(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/academic-years', [
            'name' => '2027/2028',
            'semester' => 'odd',
            'start_date' => '2027-07-10',
            'end_date' => '2027-05-01', // invalid: before start
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_only_one_academic_year_can_be_active_at_a_time(): void
    {
        $admin = User::factory()->admin()->create();

        $firstYear = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'even',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post('/admin/academic-years', [
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => '1',
        ]);

        // First year should now be inactive
        $this->assertFalse($firstYear->fresh()->is_active);
        $this->assertEquals(1, AcademicYear::where('is_active', true)->count());
    }

    public function test_admin_can_update_academic_year(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'odd',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->put("/admin/academic-years/{$year->id}", [
            'name' => '2025/2026 Revisi',
            'semester' => 'odd',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => '0',
        ]);

        $response->assertRedirect('/admin/academic-years');
        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'name' => '2025/2026 Revisi',
        ]);
    }

    public function test_admin_can_delete_unreferenced_academic_year(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2024/2025',
            'semester' => 'odd',
            'start_date' => '2024-07-01',
            'end_date' => '2024-12-31',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/academic-years/{$year->id}");

        $response->assertRedirect('/admin/academic-years');
        $this->assertDatabaseMissing('academic_years', ['id' => $year->id]);
    }

    public function test_cannot_delete_academic_year_with_enrollments(): void
    {
        $admin = User::factory()->admin()->create();
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '999888', 'gender' => 'male']);
        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/academic-years/{$year->id}");

        $response->assertRedirect('/admin/academic-years');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('academic_years', ['id' => $year->id]);
    }
}
