<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::role('mahasiswa')->first();
        $book = Book::where('is_available', true)->where('available_stock', '>', 0)->first();

        if (! $user || ! $book) {
            $this->command->error('Need user and book to create test loans');

            return;
        }

        // Active loan
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'quantity' => 1,
            'processed_by' => User::role('admin')->first()->id,
            'loan_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'active',
        ]);

        $this->command->info(' Test loan created');
    }
}
