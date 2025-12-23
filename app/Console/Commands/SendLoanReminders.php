<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendLoanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder ke user yang akan jatuh tempo (H-3, H-1, H-day)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending loan reminders...');

        $reminders = [
            'due_today' => now()->format('Y-m-d'),
            'due_tomorrow' => now()->addDay()->format('Y-m-d'),
            'due_in_3_days' => now()->addDays(3)->format('Y-m-d'),
        ];

        $totalSent = 0;

        foreach ($reminders as $type => $date) {
            $loans = Loan::whereIn('status', ['active', 'extended'])
                ->whereDate('due_date', $date)
                ->with(['user', 'bookItem.book'])
                ->get();

            if ($loans->isEmpty()) {
                continue;
            }

            $this->newLine();
            $this->info("📧 Sending reminders for: " . str_replace('_', ' ', $type) . " ({$date})");

            $bar = $this->output->createProgressBar($loans->count());
            $bar->start();

            foreach ($loans as $loan) {
                // Send reminder notification
                $this->sendReminderNotification($loan, $type);

                $totalSent++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->line("   Sent {$loans->count()} reminder(s)");

            // Show details
            $this->table(
                ['User', 'Book', 'Due Date'],
                $loans->map(function ($loan) {
                    return [
                        $loan->user->name . ' (' . $loan->user->nim . ')',
                        $loan->bookItem->book->title,
                        $loan->due_date->format('d M Y'),
                    ];
                })
            );
        }

        if ($totalSent === 0) {
            $this->info('No reminders to send today.');
        } else {
            $this->newLine();
            $this->info("Total reminders sent: {$totalSent}");
        }

        return Command::SUCCESS;
    }

    /**
     * Send reminder notification to user
     */
    protected function sendReminderNotification(Loan $loan, string $type): void
    {
        // Get message based on type
        $message = match ($type) {
            'due_today' => "⚠️ REMINDER: Buku \"{$loan->bookItem->book->title}\" harus dikembalikan HARI INI!",
            'due_tomorrow' => "⏰ REMINDER: Buku \"{$loan->bookItem->book->title}\" harus dikembalikan BESOK ({$loan->due_date->format('d M Y')}).",
            'due_in_3_days' => "📅 REMINDER: Buku \"{$loan->bookItem->book->title}\" harus dikembalikan dalam 3 hari ({$loan->due_date->format('d M Y')}).",
            default => "Reminder: Please return your book.",
        };

        // Implement actual notification sending
        // Options:
        // 1. Email: Mail::to($loan->user->email)->send(new LoanReminderMail($loan, $message));
        // 2. WhatsApp: Send via WhatsApp API
        // 3. In-app: Create notification record
        // 4. SMS: Send via SMS gateway

        // For now, just log
        Log::info("Reminder sent to {$loan->user->email}: {$message}");

        // Example: Create in-app notification (if you have notifications table)
        // $loan->user->notify(new LoanReminderNotification($loan, $type));
    }
}
