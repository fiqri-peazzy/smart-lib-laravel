<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class UpdateOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:update-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status peminjaman yang sudah melewati due date menjadi overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue loans...');

        // Get all active/extended loans that are past due date
        $overdueLoans = Loan::whereIn('status', ['active', 'extended'])
            ->where('due_date', '<', now())
            ->whereNull('return_date')
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info('No overdue loans found.');
            return Command::SUCCESS;
        }

        $count = 0;
        $bar = $this->output->createProgressBar($overdueLoans->count());
        $bar->start();

        foreach ($overdueLoans as $loan) {
            // Update status to overdue
            $loan->update(['status' => 'overdue']);

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$count} loan(s) to overdue status.");

        // Show summary
        $this->table(
            ['User', 'Book', 'Due Date', 'Days Overdue'],
            $overdueLoans->map(function ($loan) {
                return [
                    $loan->user->name . ' (' . $loan->user->nim . ')',
                    $loan->bookItem->book->title,
                    $loan->due_date->format('d M Y'),
                    abs($loan->getDaysOverdue()) . ' days',
                ];
            })
        );

        return Command::SUCCESS;
    }
}
