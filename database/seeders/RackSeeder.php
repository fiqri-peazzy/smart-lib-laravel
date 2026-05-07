<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $racks = [
            ['code' => 'R-001', 'name' => 'Rak Ilmu Komputer', 'description' => 'Rak buku untuk jurusan teknik informatika'],
            ['code' => 'R-002', 'name' => 'Rak Sistem Informasi', 'description' => 'Rak buku untuk jurusan sistem informasi'],
            ['code' => 'R-003', 'name' => 'Rak Umum', 'description' => 'Rak buku untuk umum'],
        ];

        foreach ($racks as $rack) {
            \App\Models\Rack::create($rack);
        }
    }
}
