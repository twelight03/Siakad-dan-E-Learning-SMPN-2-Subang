<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun Siswa');
    }

    public function test_new_students_can_register_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'username' => 'budipratama',
            'nis' => '202607999',
            'gender' => 'male',
            'email' => 'budipratama@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'budipratama@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isStudent());
        $this->assertEquals('budipratama', $user->username);

        $student = Student::where('user_id', $user->id)->first();
        $this->assertNotNull($student);
        $this->assertEquals('202607999', $student->nis);
        $this->assertEquals('male', $student->gender);

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_fails_when_password_does_not_match(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Pratama',
            'username' => 'budipratama2',
            'nis' => '202607998',
            'gender' => 'male',
            'email' => 'budipratama2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_fails_when_nis_is_duplicate(): void
    {
        // First registration
        $this->post('/register', [
            'name' => 'Siswa Pertama',
            'username' => 'siswa1',
            'nis' => '202607111',
            'gender' => 'female',
            'email' => 'siswa1@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        auth()->logout();

        // Second registration with same NIS
        $response = $this->post('/register', [
            'name' => 'Siswa Kedua',
            'username' => 'siswa2',
            'nis' => '202607111',
            'gender' => 'male',
            'email' => 'siswa2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nis');
        $this->assertGuest();
    }
}
