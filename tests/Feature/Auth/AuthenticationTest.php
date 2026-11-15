<?php

namespace Tests\Feature\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIAKAD SMPN 2 Subang');
        $response->assertSee('Nama Pengguna atau Email');
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->student()->create([
            'username' => 'ahmadfauzan',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'username' => 'ahmadfauzan',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_users_can_authenticate_using_email(): void
    {
        $user = User::factory()->teacher()->create([
            'email' => 'guru@smpn2subang.sch.id',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'username' => 'guru@smpn2subang.sch.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('teacher.dashboard', absolute: false));
    }

    public function test_admin_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin_demo',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'username' => 'admin_demo',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $inactiveUser = User::factory()->inactive()->create([
            'username' => 'akun_nonaktif',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'username' => 'akun_nonaktif',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'username' => 'user_valid',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'username' => 'user_valid',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_login_activity_is_logged_in_database(): void
    {
        $user = User::factory()->admin()->create([
            'username' => 'admin_logger',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'username' => 'admin_logger',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'login',
        ]);
    }
}
