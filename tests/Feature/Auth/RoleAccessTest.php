<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_teacher_dashboard(): void
    {
        $response = $this->get('/teacher/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_student_dashboard(): void
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_teacher_forbidden_from_admin_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_teacher_can_access_teacher_dashboard(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get('/teacher/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_forbidden_from_admin_dashboard(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_student_forbidden_from_teacher_dashboard(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/teacher/dashboard');

        $response->assertStatus(403);
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get('/student/dashboard');

        $response->assertStatus(200);
    }
}
