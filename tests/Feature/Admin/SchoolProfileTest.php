<?php

namespace Tests\Feature\Admin;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_school_profile_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.school-profile.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Data Pokok & Profil Sekolah');
        $response->assertSee('Mode Manual Sensus Dapodik');
    }

    public function test_non_admin_cannot_access_school_profile_page(): void
    {
        $teacher = User::factory()->teacher()->create();

        $response = $this->actingAs($teacher)->get(route('admin.school-profile.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_update_dapodik_settings_and_school_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.school-profile.update'), [
            'dapodik_mode' => 'manual',
            'dapodik_students' => 720,
            'dapodik_teachers' => 45,
            'dapodik_classes' => 20,
            'dapodik_subjects' => 12,
            'dapodik_students_label' => 'Siswa Terverifikasi',
            'dapodik_teachers_label' => 'Pendidik Unggul',
            'dapodik_classes_label' => 'Rombel Kelas VII-IX',
            'dapodik_subjects_label' => 'Mata Pelajaran Wajib & Muatan Lokal',
            'school_npsn' => '20212952',
            'school_accreditation' => 'A (Unggul Paripurna)',
            'school_address' => 'Jl. Raya Subang Baru No. 12',
            'school_phone' => '081234567890',
            'school_email' => 'admin@smpn2subang.sch.id',
            'principal_name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
            'principal_speech' => 'Sambutan resmi kepala sekolah baru untuk portal akademik.',
        ]);

        $response->assertRedirect(route('admin.school-profile.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(720, SchoolSetting::get('dapodik_students'));
        $this->assertEquals(45, SchoolSetting::get('dapodik_teachers'));
        $this->assertEquals(20, SchoolSetting::get('dapodik_classes'));
        $this->assertEquals('A (Unggul Paripurna)', SchoolSetting::get('school_accreditation'));
    }

    public function test_landing_page_renders_custom_dapodik_stats(): void
    {
        SchoolSetting::set('dapodik_mode', 'manual');
        SchoolSetting::set('dapodik_students', 680);
        SchoolSetting::set('dapodik_students_label', 'Siswa Prestasi');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('680');
        $response->assertSee('Siswa Prestasi');
    }
}
