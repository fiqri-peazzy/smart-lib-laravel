<?php

use App\Models\Loan;
use App\Models\Fine;
use App\Models\User;

$loan = Loan::where('status', 'overdue')->latest()->first(); 
if (!$loan) {
    echo "No overdue loan found. Run create_test_overdue.php first!\n";
    exit;
}

echo "Processing return for Loan #{$loan->id} to generate fine...\n";

// We can just manually create a fine record if we want to test the payment gateway
$fine = Fine::create([
    'loan_id' => $loan->id,
    'user_id' => $loan->user_id,
    'amount' => $loan->calculateFine(),
    'days_overdue' => $loan->getDaysOverdue(),
    'daily_rate' => 1000,
    'status' => 'unpaid',
]);

// Update user total fines
$loan->user->increment('total_fines', $fine->amount);

echo "Fine Created! ID: {$fine->id}\n";
echo "Amount: Rp " . number_format($fine->amount, 0) . "\n";
echo "User: {$loan->user->name}\n";
