<?php

use App\Models\User;
use App\Models\BookItem;
use App\Models\Loan;
use Carbon\Carbon;

$user = User::role('mahasiswa')->first();
if (!$user) {
    echo "No mahasiswa found!\n";
    exit;
}

$item = BookItem::where('status', 'available')->first();
if (!$item) {
    echo "No available book item found!\n";
    exit;
}

echo "Creating overdue loan for User: {$user->name} ({$user->email})\n";
echo "Book Item: #{$item->id}\n";

// Create loan
$loan = Loan::create([
    'user_id' => $user->id,
    'book_item_id' => $item->id,
    'loan_date' => Carbon::now()->subDays(20),
    'due_date' => Carbon::now()->subDays(5), // Overdue by 5 days
    'status' => 'active',
]);

// Trigger overdue update if boot doesn't handle it immediately for manually set dates
if ($loan->isOverdue()) {
    $loan->update(['status' => 'overdue']);
}

echo "Loan Created successfully! ID: {$loan->id}\n";
echo "Status: {$loan->status}\n";
echo "Due Date: {$loan->due_date->format('Y-m-d')}\n";
echo "Days Overdue: {$loan->getDaysOverdue()}\n";
echo "Potential Fine: Rp " . number_format($loan->calculateFine(), 0) . "\n";
