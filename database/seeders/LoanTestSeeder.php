<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BookItem;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::role('mahasiswa')->first();
        $bookItem = BookItem::where('status', 'available')->first();

        if (!$user || !$bookItem) {
            $this->command->error('Need user and book item to create test loans');
            return;
        }

        // Active loan
        Loan::create([
            'user_id' => $user->id,
            'book_item_id' => $bookItem->id,
            'processed_by' => User::role('admin')->first()->id,
            'loan_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'active',
        ]);

        $this->command->info(' Test loan created');
    }
}
