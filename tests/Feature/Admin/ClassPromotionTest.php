<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_view_promotions_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/promotions');

        $response->assertStatus(200);
        $response->assertSee('Kenaikan Kelas');
    }

    public function test_admin_can_promote_whole_class_directly(): void
    {
        $admin = User::factory()->admin()->create();

        $yearFrom = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'even',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $yearTo = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class7A = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $class8A = SchoolClass::create(['name' => 'VIII-A', 'grade_level' => '8']);

        $studentRole = Role::where('name', 'student')->first();
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '202607100', 'gender' => 'male']);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class7A->id,
            'academic_year_id' => $yearFrom->id,
        ]);

        $response = $this->actingAs($admin)->post('/admin/promotions/direct', [
            'from_class_id' => $class7A->id,
            'from_academic_year_id' => $yearFrom->id,
            'to_class_id' => $class8A->id,
            'to_academic_year_id' => $yearTo->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_enrollments', [
            'student_id' => $student->id,
            'class_id' => $class8A->id,
            'academic_year_id' => $yearTo->id,
        ]);
    }

    public function test_admin_can_distribute_students_evenly_and_randomly(): void
    {
        $admin = User::factory()->admin()->create();

        $yearFrom = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'even',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $yearTo = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        $class7A = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);
        $class8A = SchoolClass::create(['name' => 'VIII-A', 'grade_level' => '8']);
        $class8B = SchoolClass::create(['name' => 'VIII-B', 'grade_level' => '8']);

        $studentRole = Role::where('name', 'student')->first();

        for ($i = 1; $i <= 4; $i++) {
            $user = User::factory()->create(['role_id' => $studentRole->id]);
            $student = Student::create([
                'user_id' => $user->id,
                'nis' => '20260720'.$i,
                'gender' => $i % 2 === 0 ? 'female' : 'male',
            ]);
            StudentEnrollment::create([
                'student_id' => $student->id,
                'class_id' => $class7A->id,
                'academic_year_id' => $yearFrom->id,
            ]);
        }

        $response = $this->actingAs($admin)->post('/admin/promotions/distribute/apply', [
            'from_class_ids' => [$class7A->id],
            'from_academic_year_id' => $yearFrom->id,
            'to_class_ids' => [$class8A->id, $class8B->id],
            'to_academic_year_id' => $yearTo->id,
            'balance_gender' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, StudentEnrollment::where('class_id', $class8A->id)->where('academic_year_id', $yearTo->id)->count());
        $this->assertEquals(2, StudentEnrollment::where('class_id', $class8B->id)->where('academic_year_id', $yearTo->id)->count());
    }
}
