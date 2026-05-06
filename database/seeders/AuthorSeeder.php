<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['name' => 'Fiersa Besari', 'biography' => 'Penulis dan pemusik asal Indonesia.'],
            ['name' => 'Tere Liye', 'biography' => 'Penulis produktif Indonesia dengan berbagai genre.'],
            ['name' => 'Pramoedya Ananta Toer', 'biography' => 'Sastrawan besar Indonesia.'],
            ['name' => 'Andrea Hirata', 'biography' => 'Penulis Laskar Pelangi.'],
            ['name' => 'Dee Lestari', 'biography' => 'Penulis seri Supernova.'],
        ];

        foreach ($authors as $author) {
            \App\Models\Author::updateOrCreate(['name' => $author['name']], $author);
        }
    }

}
