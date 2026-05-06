<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            ['name' => 'Gramedia Pustaka Utama', 'address' => 'Jakarta', 'email' => 'contact@gramedia.id'],
            ['name' => 'Bentang Pustaka', 'address' => 'Yogyakarta', 'email' => 'info@bentangpustaka.com'],
            ['name' => 'Mizan', 'address' => 'Bandung', 'email' => 'redaksi@mizan.com'],
            ['name' => 'Penerbit Erlangga', 'address' => 'Jakarta', 'email' => 'support@erlangga.co.id'],
        ];

        foreach ($publishers as $publisher) {
            \App\Models\Publisher::updateOrCreate(['name' => $publisher['name']], $publisher);
        }
    }

}
