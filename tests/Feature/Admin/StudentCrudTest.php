<?php

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_students_list(): void
    {
        $admin = User::factory()->admin()->create();
        $studentUser = User::factory()->student()->create(['name' => 'Bambang Pamungkas']);
        Student::create(['user_id' => $studentUser->id, 'nis' => '10203040', 'gender' => 'male']);

        $response = $this->actingAs($admin)->get('/admin/students');

        $response->assertStatus(200);
        $response->assertSee('Bambang Pamungkas');
        $response->assertSee('10203040');
    }

    public function test_non_admin_cannot_access_students(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/admin/students');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_student_and_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/students', [
            'name' => 'Fajar Alfian',
            'username' => 'siswa_fajar',
            'email' => 'fajar@smpn2subang.sch.id',
            'password' => 'secret123',
            'nis' => '202607999',
            'gender' => 'male',
            'birth_date' => '2012-04-10',
            'phone' => '089988776655',
            'address' => 'Jl. Eyang Tirta, Subang',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('users', [
            'username' => 'siswa_fajar',
            'name' => 'Fajar Alfian',
        ]);
        $this->assertDatabaseHas('students', [
            'nis' => '202607999',
            'gender' => 'male',
        ]);
    }

    public function test_student_nis_and_username_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $existingUser = User::factory()->student()->create(['username' => 'siswa_unik']);
        Student::create(['user_id' => $existingUser->id, 'nis' => '20260001', 'gender' => 'male']);

        $response = $this->actingAs($admin)->post('/admin/students', [
            'name' => 'Siswa Baru',
            'username' => 'siswa_unik', // duplicate username
            'password' => 'secret123',
            'nis' => '20260001', // duplicate nis
            'gender' => 'female',
        ]);

        $response->assertSessionHasErrors(['username', 'nis']);
    }

    public function test_admin_can_update_student_data(): void
    {
        $admin = User::factory()->admin()->create();
        $studentUser = User::factory()->student()->create(['username' => 'siswa_revisi', 'name' => 'Nama Sebelum']);
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '20260002', 'gender' => 'female']);

        $response = $this->actingAs($admin)->put("/admin/students/{$student->id}", [
            'name' => 'Nama Sesudah',
            'username' => 'siswa_sesudah',
            'nis' => '20260002',
            'gender' => 'female',
            'phone' => '087711223344',
        ]);

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseHas('users', [
            'id' => $studentUser->id,
            'name' => 'Nama Sesudah',
            'username' => 'siswa_sesudah',
        ]);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'phone' => '087711223344',
        ]);
    }

    public function test_admin_can_delete_unreferenced_student(): void
    {
        $admin = User::factory()->admin()->create();
        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '20260003', 'gender' => 'male']);

        $response = $this->actingAs($admin)->delete("/admin/students/{$student->id}");

        $response->assertRedirect('/admin/students');
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $studentUser->id]);
    }

    public function test_cannot_delete_student_with_enrollments(): void
    {
        $admin = User::factory()->admin()->create();
        $studentUser = User::factory()->student()->create();
        $student = Student::create(['user_id' => $studentUser->id, 'nis' => '20260004', 'gender' => 'male']);

        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'odd',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
        $class = SchoolClass::create(['name' => 'VII-A', 'grade_level' => '7']);

        StudentEnrollment::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/students/{$student->id}");

        $response->assertRedirect('/admin/students');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }
}
