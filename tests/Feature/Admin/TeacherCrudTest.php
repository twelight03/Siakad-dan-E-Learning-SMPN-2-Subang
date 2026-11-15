<?php

namespace Tests\Feature\Admin;

use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_teachers_list(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create(['name' => 'Drs. Supardi']);
        Teacher::create(['user_id' => $teacherUser->id, 'nip' => '196501011990011001']);

        $response = $this->actingAs($admin)->get('/admin/teachers');

        $response->assertStatus(200);
        $response->assertSee('Drs. Supardi');
        $response->assertSee('196501011990011001');
    }

    public function test_non_admin_cannot_access_teachers(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/teachers');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_teacher_and_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/teachers', [
            'name' => 'Endang Kurnia, M.Pd.',
            'username' => 'guru_endang',
            'email' => 'endang@smpn2subang.sch.id',
            'password' => 'secret123',
            'nip' => '197505052000031002',
            'gender' => 'male',
            'phone' => '081399887766',
            'address' => 'Subang Kota',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/teachers');
        $this->assertDatabaseHas('users', [
            'username' => 'guru_endang',
            'name' => 'Endang Kurnia, M.Pd.',
        ]);
        $this->assertDatabaseHas('teachers', [
            'nip' => '197505052000031002',
            'gender' => 'male',
        ]);
    }

    public function test_teacher_nip_and_username_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        $existingUser = User::factory()->teacher()->create(['username' => 'guru_kembar']);
        Teacher::create(['user_id' => $existingUser->id, 'nip' => '11223344']);

        $response = $this->actingAs($admin)->post('/admin/teachers', [
            'name' => 'Guru Lain',
            'username' => 'guru_kembar', // duplicate username
            'password' => 'secret123',
            'nip' => '11223344', // duplicate nip
        ]);

        $response->assertSessionHasErrors(['username', 'nip']);
    }

    public function test_admin_can_update_teacher_and_user_data(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create(['username' => 'guru_lama', 'name' => 'Nama Lama']);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '998811']);

        $response = $this->actingAs($admin)->put("/admin/teachers/{$teacher->id}", [
            'name' => 'Nama Baru, S.Pd.',
            'username' => 'guru_baru',
            'nip' => '998811',
            'gender' => 'female',
            'phone' => '081299990000',
        ]);

        $response->assertRedirect('/admin/teachers');
        $this->assertDatabaseHas('users', [
            'id' => $teacherUser->id,
            'name' => 'Nama Baru, S.Pd.',
            'username' => 'guru_baru',
        ]);
        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'gender' => 'female',
            'phone' => '081299990000',
        ]);
    }

    public function test_admin_can_delete_unreferenced_teacher(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '888777']);

        $response = $this->actingAs($admin)->delete("/admin/teachers/{$teacher->id}");

        $response->assertRedirect('/admin/teachers');
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('users', ['id' => $teacherUser->id]);
    }

    public function test_cannot_delete_teacher_who_is_homeroom(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create();
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'nip' => '777666']);

        SchoolClass::create([
            'name' => 'VII-Wali',
            'grade_level' => '7',
            'homeroom_teacher_id' => $teacher->id,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/teachers/{$teacher->id}");

        $response->assertRedirect('/admin/teachers');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id]);
    }
}
