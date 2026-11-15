<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $studentRole = Role::where('name', 'student')->first();

        // 1. Admin Demo
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Administrator SIAKAD',
                'email' => 'admin@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 2. Teacher Demo (Budi Santoso)
        $teacherUser1 = User::firstOrCreate(
            ['username' => 'guru'],
            [
                'role_id' => $teacherRole->id,
                'name' => 'Budi Santoso, S.Pd.',
                'email' => 'budi@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Teacher::firstOrCreate(
            ['user_id' => $teacherUser1->id],
            [
                'nip' => '198501012010011001',
                'gender' => 'male',
                'phone' => '081234567890',
                'address' => 'Jl. Dangdeur No. 12, Subang',
            ]
        );

        // Teacher 2 (Siti Rahma)
        $teacherUser2 = User::firstOrCreate(
            ['username' => 'guru_siti'],
            [
                'role_id' => $teacherRole->id,
                'name' => 'Siti Rahma, M.Pd.',
                'email' => 'siti@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Teacher::firstOrCreate(
            ['user_id' => $teacherUser2->id],
            [
                'nip' => '198802022012022002',
                'gender' => 'female',
                'phone' => '081234567891',
                'address' => 'Jl. Otista No. 45, Subang',
            ]
        );

        // 3. Student Demo (Ahmad Fauzan)
        $studentUser1 = User::firstOrCreate(
            ['username' => 'siswa'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Ahmad Fauzan',
                'email' => 'ahmad@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Student::firstOrCreate(
            ['user_id' => $studentUser1->id],
            [
                'nis' => '202607001',
                'gender' => 'male',
                'birth_date' => '2012-05-14',
                'address' => 'Jl. Pejuang No. 5, Subang',
                'phone' => '085712345678',
            ]
        );

        // Student 2 (Dewi Lestari)
        $studentUser2 = User::firstOrCreate(
            ['username' => 'siswa_dewi'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Dewi Lestari',
                'email' => 'dewi@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Student::firstOrCreate(
            ['user_id' => $studentUser2->id],
            [
                'nis' => '202607002',
                'gender' => 'female',
                'birth_date' => '2012-08-22',
                'address' => 'Jl. Veteran No. 10, Subang',
                'phone' => '085712345679',
            ]
        );

        // Student 3 (Rizky Pratama)
        $studentUser3 = User::firstOrCreate(
            ['username' => 'siswa_rizky'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Rizky Pratama',
                'email' => 'rizky@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Student::firstOrCreate(
            ['user_id' => $studentUser3->id],
            [
                'nis' => '202607003',
                'gender' => 'male',
                'birth_date' => '2012-11-03',
                'address' => 'Jl. Sompi No. 2, Subang',
                'phone' => '085712345680',
            ]
        );

        // Inactive User Demo for Testing
        User::firstOrCreate(
            ['username' => 'nonaktif'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Akun Dinonaktifkan',
                'email' => 'nonaktif@siakad.smpn2subang.sch.id',
                'password' => Hash::make('password'),
                'is_active' => false,
            ]
        );
    }
}
