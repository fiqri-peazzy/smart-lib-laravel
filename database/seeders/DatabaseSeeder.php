<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Majors (Jurusan/Prodi)
        $this->call(MajorSeeder::class);

        // 2. Seed Roles & Permissions (Spatie)
        $this->call(RolePermissionSeeder::class);

        // 3. Create default users for testing
        $this->createDefaultUsers();
    }

    /**
     * Create default users dengan berbagai role
     */
    private function createDefaultUsers(): void
    {
        // Get majors
        $ifMajor = Major::where('code', 'IF')->first();
        $siMajor = Major::where('code', 'SI')->first();

        // 1. ADMIN
        $admin = User::create([
            'nim' => null,
            'username' => 'admin',
            'email' => 'admin@ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Admin Perpustakaan',
            'phone' => '081234567890',
            'major_id' => null,
            'angkatan' => null,
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // 2. STAFF PUSTAKAWAN
        $staff = User::create([
            'nim' => null,
            'username' => 'staff',
            'email' => 'staff@ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Staff Perpustakaan',
            'phone' => '081234567891',
            'major_id' => null,
            'angkatan' => null,
            'status' => 'active',
        ]);
        $staff->assignRole('staff');

        // 3. DOSEN
        $dosen = User::create([
            'nim' => '199001012020121001',
            'username' => 'dosen.if',
            'email' => 'dosen@ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Dr. Ahmad Dosen, S.Kom., M.T.',
            'phone' => '081234567892',
            'card_number' => 'DSN-2020-001',
            'major_id' => $ifMajor->id,
            'angkatan' => 2020,
            'credit_score' => 100,
            'max_loans' => 8, // Dosen dapat lebih banyak
            'status' => 'active',
        ]);
        $dosen->assignRole('dosen');

        // 4. MAHASISWA IF (Good standing)
        $mhsGood = User::create([
            'nim' => '2021310001',
            'username' => 'john.doe',
            'email' => 'john.doe@student.ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'John Doe',
            'phone' => '081234567893',
            'card_number' => 'MHS-2021-001',
            'major_id' => $ifMajor->id,
            'angkatan' => 2021,
            'credit_score' => 95,
            'max_loans' => 4,
            'status' => 'active',
        ]);
        $mhsGood->assignRole('mahasiswa');

        // 5. MAHASISWA SI (With fines)
        $mhsFines = User::create([
            'nim' => '2022320002',
            'username' => 'jane.smith',
            'email' => 'jane.smith@student.ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Jane Smith',
            'phone' => '081234567894',
            'card_number' => 'MHS-2022-002',
            'major_id' => $siMajor->id,
            'angkatan' => 2022,
            'credit_score' => 75,
            'max_loans' => 3,
            'total_fines' => 25000,
            'status' => 'active',
        ]);
        $mhsFines->assignRole('mahasiswa');

        // 6. MAHASISWA (Low credit score)
        $mhsLow = User::create([
            'nim' => '2020310003',
            'username' => 'bad.borrower',
            'email' => 'bad@student.ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Bad Borrower',
            'phone' => '081234567895',
            'card_number' => 'MHS-2020-003',
            'major_id' => $ifMajor->id,
            'angkatan' => 2020,
            'credit_score' => 45,
            'max_loans' => 1,
            'total_fines' => 75000,
            'status' => 'active',
        ]);
        $mhsLow->assignRole('mahasiswa');

        // 7. MAHASISWA SUSPENDED
        $mhsSuspended = User::create([
            'nim' => '2019310004',
            'username' => 'suspended.user',
            'email' => 'suspended@student.ichsan.ac.id',
            'password' => bcrypt('password'),
            'name' => 'Suspended User',
            'phone' => '081234567896',
            'card_number' => 'MHS-2019-004',
            'major_id' => $ifMajor->id,
            'angkatan' => 2019,
            'credit_score' => 20,
            'max_loans' => 0,
            'total_fines' => 150000,
            'status' => 'suspended',
        ]);
        $mhsSuspended->assignRole('mahasiswa');

        $this->command->info('Default users created successfully!');
        $this->command->info('Email: admin@ichsan.ac.id | Password: password');
        $this->command->info('Email: staff@ichsan.ac.id | Password: password');
        $this->command->info('Email: dosen@ichsan.ac.id | Password: password');
        $this->command->info('Email: john.doe@student.ichsan.ac.id | Password: password');
    }
}
