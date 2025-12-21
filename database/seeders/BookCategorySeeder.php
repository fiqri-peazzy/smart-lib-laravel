<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use Illuminate\Database\Seeder;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Programming',
                'description' => 'Buku tentang pemrograman dan coding',
                'color' => '#3b82f6', // Blue
            ],
            [
                'name' => 'Database',
                'description' => 'Buku tentang database dan manajemen data',
                'color' => '#10b981', // Green
            ],
            [
                'name' => 'Networking',
                'description' => 'Buku tentang jaringan komputer',
                'color' => '#8b5cf6', // Purple
            ],
            [
                'name' => 'Web Development',
                'description' => 'Buku tentang pengembangan web',
                'color' => '#f59e0b', // Orange
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Buku tentang pengembangan aplikasi mobile',
                'color' => '#06b6d4', // Cyan
            ],
            [
                'name' => 'Artificial Intelligence',
                'description' => 'Buku tentang AI dan machine learning',
                'color' => '#ec4899', // Pink
            ],
            [
                'name' => 'Data Science',
                'description' => 'Buku tentang data science dan analytics',
                'color' => '#6366f1', // Indigo
            ],
            [
                'name' => 'Cyber Security',
                'description' => 'Buku tentang keamanan siber',
                'color' => '#ef4444', // Red
            ],
            [
                'name' => 'Software Engineering',
                'description' => 'Buku tentang rekayasa perangkat lunak',
                'color' => '#14b8a6', // Teal
            ],
            [
                'name' => 'Operating Systems',
                'description' => 'Buku tentang sistem operasi',
                'color' => '#84cc16', // Lime
            ],
            [
                'name' => 'Cloud Computing',
                'description' => 'Buku tentang cloud dan distributed systems',
                'color' => '#0ea5e9', // Sky
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'Buku tentang desain antarmuka pengguna',
                'color' => '#f97316', // Orange
            ],
            [
                'name' => 'Algorithm & Data Structure',
                'description' => 'Buku tentang algoritma dan struktur data',
                'color' => '#a855f7', // Purple
            ],
            [
                'name' => 'Computer Architecture',
                'description' => 'Buku tentang arsitektur komputer',
                'color' => '#64748b', // Slate
            ],
            [
                'name' => 'General IT',
                'description' => 'Buku umum tentang teknologi informasi',
                'color' => '#6b7280', // Gray
            ],
        ];

        foreach ($categories as $category) {
            BookCategory::create($category);
        }

        $this->command->info('Book categories seeded successfully!');
    }
}
