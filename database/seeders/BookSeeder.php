<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookItem;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan user admin untuk field added_by
        $admin = User::first();
        if (!$admin) {
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
            /* Programming */
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

            /* Web Development */
            [
                'title' => 'Learning Web Design',
                'subtitle' => 'A Beginner\'s Guide to HTML, CSS, JavaScript, and Web Graphics',
                'isbn' => '9781491960301',
                'author' => 'Jennifer Robbins',
                'publisher' => 'O\'Reilly Media',
                'year' => 2018,
                'category' => 'Web Development',
                'description' => 'Do you want to build web pages but have no previous experience? This friendly guide is the perfect place to start.',
            ],
            [
                'title' => 'Fullstack Laravel',
                'subtitle' => 'The Complete Guide to Laravel, Vue, and Tailwind',
                'isbn' => '9781491960302',
                'author' => 'Laravel Experts',
                'publisher' => 'Tech Press',
                'year' => 2023,
                'category' => 'Web Development',
                'description' => 'Learn how to build modern web applications using the TALL stack.',
            ],

            /* Database */
            [
                'title' => 'Database System Concepts',
                'subtitle' => 'Seventh Edition',
                'isbn' => '9780073523323',
                'author' => 'Abraham Silberschatz',
                'publisher' => 'McGraw-Hill',
                'year' => 2019,
                'category' => 'Database',
                'description' => 'Database System Concepts by Silberschatz, Korth and Sudarshan is now in its 7th edition.',
            ],
            [
                'title' => 'SQL Performance Explained',
                'subtitle' => 'Everything developers need to know about SQL performance',
                'isbn' => '9783950307825',
                'author' => 'Markus Winand',
                'publisher' => 'Winand',
                'year' => 2012,
                'category' => 'Database',
                'description' => 'A book about indexing and SQL performance for developers.',
            ],

            /* Artificial Intelligence */
            [
                'title' => 'Hands-On Machine Learning',
                'subtitle' => 'with Scikit-Learn, Keras, and TensorFlow',
                'isbn' => '9781492032649',
                'author' => 'Aurélien Géron',
                'publisher' => 'O\'Reilly Media',
                'year' => 2019,
                'category' => 'Artificial Intelligence',
                'description' => 'Through a series of recent breakthroughs, deep learning has boosted the entire field of machine learning.',
            ],
            [
                'title' => 'Artificial Intelligence',
                'subtitle' => 'A Modern Approach',
                'isbn' => '9780136042594',
                'author' => 'Stuart Russell, Peter Norvig',
                'publisher' => 'Pearson',
                'year' => 2020,
                'category' => 'Artificial Intelligence',
                'description' => 'The long-anticipated revision of this best-selling text offers the most comprehensive, up-to-date introduction to the theory and practice of artificial intelligence.',
            ],

            /* Networking & Cyber Security */
            [
                'title' => 'Computer Networking',
                'subtitle' => 'A Top-Down Approach',
                'isbn' => '9780133594140',
                'author' => 'James Kurose, Keith Ross',
                'publisher' => 'Pearson',
                'year' => 2016,
                'category' => 'Networking',
                'description' => 'Focusing on the Internet and the fundamentally important issues of networking.',
            ],
            [
                'title' => 'The Art of Invisibility',
                'subtitle' => 'The World\'s Most Famous Hacker Teaches You How to Be Safe in the Age of Big Brother and Big Data',
                'isbn' => '9780316380508',
                'author' => 'Kevin Mitnick',
                'publisher' => 'Little, Brown and Company',
                'year' => 2017,
                'category' => 'Cyber Security',
                'description' => 'Kevin Mitnick, the world\'s most famous hacker, teaches you how to keep your life safe and private on the internet.',
            ],

            /* Software Engineering */
            [
                'title' => 'Refactoring',
                'subtitle' => 'Improving the Design of Existing Code',
                'isbn' => '9780134757599',
                'author' => 'Martin Fowler',
                'publisher' => 'Addison-Wesley',
                'year' => 2018,
                'category' => 'Software Engineering',
                'description' => 'Refactoring is a controlled technique for improving the design of an existing code base.',
            ],
            [
                'title' => 'Mythical Man-Month, The',
                'subtitle' => 'Essays on Software Engineering',
                'isbn' => '9780201835953',
                'author' => 'Frederick Brooks Jr.',
                'publisher' => 'Addison-Wesley',
                'year' => 1995,
                'category' => 'Software Engineering',
                'description' => 'Few books on software project management have been as influential and timeless as The Mythical Man-Month.',
            ],

            /* UI/UX Design */
            [
                'title' => 'Don\'t Make Me Think',
                'subtitle' => 'A Common Sense Approach to Web Usability',
                'isbn' => '9780321965516',
                'author' => 'Steve Krug',
                'publisher' => 'New Riders',
                'year' => 2014,
                'category' => 'UI/UX Design',
                'description' => 'Since Don\'t Make Me Think was first published in 2000, hundreds of thousands of Web designers and developers have relied on usability guru Steve Krug\'s guide.',
            ],
            [
                'title' => 'The Design of Everyday Things',
                'subtitle' => 'Revised and Expanded Edition',
                'isbn' => '9780465050659',
                'author' => 'Don Norman',
                'publisher' => 'Basic Books',
                'year' => 2013,
                'category' => 'UI/UX Design',
                'description' => 'Even the smartest among us can feel inept as we fail to figure out which light switch or oven burner to turn on, or whether to push, pull, or slide a door.',
            ],

            /* General IT */
            [
                'title' => 'Code: The Hidden Language of Computer Hardware and Software',
                'subtitle' => 'Second Edition',
                'isbn' => '9780137909100',
                'author' => 'Charles Petzold',
                'publisher' => 'Microsoft Press',
                'year' => 2022,
                'category' => 'General IT',
                'description' => 'A mesmerizing narrative that illuminates the very nature of computing.',
            ],
        ];

        foreach ($booksData as $data) {
            $book = Book::create([
                'isbn' => $data['isbn'],
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'author' => $data['author'],
                'publisher' => $data['publisher'],
                'publication_year' => $data['year'],
                'language' => 'en',
                'description' => $data['description'],
                'rack_location' => 'RAK-' . chr(rand(65, 70)) . '-' . rand(1, 10),
                'recommended_for_major_id' => $majors->isNotEmpty() ? $majors->random()->id : null,
                'is_available' => true,
                'is_featured' => rand(0, 1) === 1,
                'added_by' => $admin->id,
                'pages' => rand(200, 800),
            ]);

            // Hubungkan dengan kategori
            $category = $categories->where('name', $data['category'])->first();
            if ($category) {
                $book->categories()->attach($category->id);
            } else {
                // Fallback jika nama kategori tidak persis sama
                $book->categories()->attach($categories->random()->id);
            }

            // Tambahkan item (eksemplar) untuk buku ini
            $numItems = rand(2, 5);
            for ($i = 0; $i < $numItems; $i++) {
                BookItem::create([
                    'book_id' => $book->id,
                    'condition' => 'excellent',
                    'status' => 'available',
                    'current_location' => 'Library Shelf',
                    'acquisition_date' => now()->subMonths(rand(1, 24)),
                    'acquisition_price' => rand(200000, 700000),
                    'notes' => 'Generated by seeder',
                ]);
            }
        }

        $this->command->info('BookSeeder: ' . count($booksData) . ' books and their items created successfully!');
    }
}
