<?php

use App\Models\DigitalCollection;
use App\Models\Major;
use App\Models\User;
use Illuminate\Support\Str;

$admin = User::role('admin')->first() ?? User::first();
$majors = Major::all();

if ($majors->isEmpty()) {
    echo "No majors found! Please seed majors first.\n";
    exit;
}

$types = ['ebook', 'jurnal', 'skripsi', 'paper', 'modul'];
$titles = [
    'ebook' => ['Mastering Laravel 11', 'Clean Code in PHP', 'Modern UI Design with TailwindCSS', 'Database Optimization Techniques'],
    'jurnal' => ['AI in Modern Agriculture', 'Blockchain Security Analysis', 'The Future of Cloud Computing', 'Quantum Computing: A Review'],
    'skripsi' => ['Sistem Informasi Perpustakaan Berbasis Web', 'Analisis Sentimen Twitter dengan Naive Bayes', 'Implementasi QR Code pada Sistem Inventaris', 'Optimasi Rute dengan Algoritma Genetika'],
    'paper' => ['Deep Learning for Image Recognition', 'Big Data Trends in 2024', 'Internet of Things Security Protocols', 'Agile Software Development Methodology'],
    'modul' => ['Pemrograman Dasar Java', 'Struktur Data & Algoritma', 'Jaringan Komputer', 'Sistem Operasi']
];

echo "Seeding digital collections...\n";

foreach ($types as $type) {
    foreach ($titles[$type] as $title) {
        $major = $majors->random();
        
        DigitalCollection::create([
            'title' => $title,
            'type' => $type,
            'author' => 'Author ' . Str::random(5),
            'year' => rand(2020, 2025),
            'isbn' => $type === 'ebook' ? rand(1000000000, 9999999999) : null,
            'publisher' => $type === 'ebook' ? 'Tech Press' : null,
            'file_path' => 'digital/sample.pdf', // Assumes this exists for testing URL
            'file_type' => 'PDF',
            'file_size' => rand(1000000, 5000000),
            'major_id' => $major->id,
            'description' => 'Ini adalah deskripsi atau abstrak dari ' . $title . '. Berisi rangkuman penting tentang isi dokumen digital ini.',
            'keywords' => $type . ', teknologi, digital, ' . $major->name,
            'view_count' => rand(10, 500),
            'download_count' => rand(1, 100),
            'uploaded_by' => $admin->id,
            'is_public' => true,
            'is_featured' => rand(0, 1)
        ]);
        echo "Created: $title ($type)\n";
    }
}

echo "Seeding completed!\n";
