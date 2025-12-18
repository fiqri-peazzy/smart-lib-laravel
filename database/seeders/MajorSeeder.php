<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'code' => 'IF',
                'name' => 'Teknik Informatika',
                'faculty' => 'Ilmu Komputer',
                'description' => 'Program Studi Teknik Informatika mempelajari pengembangan software, algoritma, dan sistem komputer.',
                'is_active' => true,
            ],
            [
                'code' => 'SI',
                'name' => 'Sistem Informasi',
                'faculty' => 'Ilmu Komputer',
                'description' => 'Program Studi Sistem Informasi fokus pada manajemen data, analisis bisnis, dan sistem informasi enterprise.',
                'is_active' => true,
            ],
            [
                'code' => 'TI',
                'name' => 'Teknologi Informasi',
                'faculty' => 'Ilmu Komputer',
                'description' => 'Program Studi Teknologi Informasi fokus pada infrastruktur IT, jaringan, dan keamanan sistem.',
                'is_active' => true,
            ],
            [
                'code' => 'RPL',
                'name' => 'Rekayasa Perangkat Lunak',
                'faculty' => 'Ilmu Komputer',
                'description' => 'Program Studi Rekayasa Perangkat Lunak fokus pada software engineering, desain sistem, dan quality assurance.',
                'is_active' => true,
            ],
            // Tambahkan prodi lain dari fakultas lain jika diperlukan
            [
                'code' => 'OTHER',
                'name' => 'Luar Fakultas Ilmu Komputer',
                'faculty' => 'Lainnya',
                'description' => 'Untuk mahasiswa dari fakultas selain Ilmu Komputer',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
