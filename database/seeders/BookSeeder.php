<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Publisher;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan user admin untuk field added_by
        $admin = User::first();
        if (! $admin) {
            $this->command->error('No user found! Please run seeders that create a user first.');
            return;
        }

        $majors = Major::all();
        $categories = BookCategory::all();

        if ($categories->isEmpty()) {
            $this->command->error('No book categories found! Please run BookCategorySeeder first.');
            return;
        }

        $booksData = [
            [
                'title' => 'Clean Code',
                'subtitle' => 'A Handbook of Agile Software Craftsmanship',
                'isbn' => '9780132350884',
                'author' => 'Robert C. Martin',
                'publisher' => 'Prentice Hall',
                'year' => 2008,
                'category' => 'Programming',
                'description' => 'Even bad code can function. But if code isn\'t clean, it can bring a development organization to its knees.',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'subtitle' => 'Your Journey To Mastery',
                'isbn' => '9780135957059',
                'author' => 'Andrew Hunt, David Thomas',
                'publisher' => 'Addison-Wesley',
                'year' => 2019,
                'category' => 'Programming',
                'description' => 'The Pragmatic Programmer is one of those rare tech books you\'ll read, re-read, and read again over the years.',
            ],
            [
                'title' => 'Eloquent JavaScript',
                'subtitle' => 'A Modern Introduction to Programming',
                'isbn' => '9781593279509',
                'author' => 'Marijn Haverbeke',
                'publisher' => 'No Starch Press',
                'year' => 2018,
                'category' => 'Programming',
                'description' => 'JavaScript lies at the heart of almost every modern web application.',
            ],
        ];

        foreach ($booksData as $data) {
            $numItems = rand(2, 5);
            $author = Author::firstOrCreate(['name' => $data['author']]);
            $publisher = Publisher::firstOrCreate(['name' => $data['publisher']]);

            $book = Book::updateOrCreate([
                'isbn' => $data['isbn'],
            ], [
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'author_id' => $author->id,
                'publisher_id' => $publisher->id,
                'publication_year' => $data['year'],
                'language' => 'en',
                'description' => $data['description'],
                'rack_location' => 'RAK-'.chr(rand(65, 70)).'-'.rand(1, 10),
                'recommended_for_major_id' => $majors->isNotEmpty() ? $majors->random()->id : null,
                'total_stock' => $numItems,
                'available_stock' => $numItems,
                'is_available' => true,
                'is_featured' => rand(0, 1) === 1,
                'added_by' => $admin->id,
                'pages' => rand(200, 800),
                'is_digital' => false,
            ]);

            $category = $categories->where('name', $data['category'])->first();
            if ($category) {
                $book->categories()->sync([$category->id]);
            }
        }

        $digitalBooks = [
            [
                'title' => 'Implementasi Laravel Untuk Sistem Perpustakaan',
                'author' => 'Budi Santoso',
                'publisher' => 'Unisan Press',
                'year' => 2024,
                'type' => 'skripsi',
                'nim' => '123456789',
                'supervisor' => 'Dr. Ahmad Yani',
                'category' => 'Software Engineering',
            ],
            [
                'title' => 'Panduan Modern Web Development 2024',
                'author' => 'Tere Liye',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => 2024,
                'type' => 'ebook',
                'category' => 'Web Development',
            ]
        ];

        foreach ($digitalBooks as $data) {
            $author = Author::firstOrCreate(['name' => $data['author']]);
            $publisher = Publisher::firstOrCreate(['name' => $data['publisher']]);

            $book = Book::updateOrCreate([
                'title' => $data['title'],
            ], [
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'author_id' => $author->id,
                'publisher_id' => $publisher->id,
                'publication_year' => $data['year'],
                'is_digital' => true,
                'digital_file_type' => $data['type'],
                'digital_file_path' => 'digital-books/sample.pdf',
                'digital_file_size' => 1024 * 1024 * 2,
                'nim' => $data['nim'] ?? null,
                'supervisor' => $data['supervisor'] ?? null,
                'keywords' => 'laravel, library, web',
                'added_by' => $admin->id,
                'is_available' => true,
            ]);

            $category = $categories->where('name', $data['category'])->first();
            if ($category) {
                $book->categories()->sync([$category->id]);
            }
        }

        $this->command->info('BookSeeder: Physical and Digital books created successfully!');
    }
}
