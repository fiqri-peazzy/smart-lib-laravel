<?php

use App\Models\Loan;
use App\Models\Fine;
use App\Models\User;

$loan = Loan::find(7); // ID from previous step
if (!$loan) {
    echo "Loan #7 not found!\n";
    exit;
}

echo "Processing return for Loan #7 to generate fine...\n";

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
