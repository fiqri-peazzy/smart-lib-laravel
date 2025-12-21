<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RecalculateCreditScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:recalculate-credit-scores
                            {--user= : Specific user ID to recalculate}
                            {--role= : Recalculate for specific role (mahasiswa, dosen)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate credit scores untuk semua user atau user tertentu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating credit scores...');

        // Build query
        $query = User::query();

        // Filter by user ID
        if ($this->option('user')) {
            $query->where('id', $this->option('user'));
        }

        // Filter by role
        if ($this->option('role')) {
            $query->role($this->option('role'));
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->error('No users found.');
            return Command::FAILURE;
        }

        $this->info("Found {$users->count()} user(s) to process.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $results = [];

        foreach ($users as $user) {
            $oldScore = $user->credit_score;
            $oldMaxLoans = $user->max_loans;

            // Update loan history
            $user->updateLoanHistory();

            // Recalculate credit score
            $user->recalculateCreditScore();

            // Update max loans
            $user->updateMaxLoans();

            // Reload to get fresh data
            $user->refresh();

            $results[] = [
                'user' => $user->name . ' (' . $user->nim . ')',
                'old_score' => number_format($oldScore, 2),
                'new_score' => number_format($user->credit_score, 2),
                'old_max' => $oldMaxLoans,
                'new_max' => $user->max_loans,
                'change' => $user->credit_score - $oldScore > 0 ? '↑' : ($user->credit_score - $oldScore < 0 ? '↓' : '='),
            ];

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Show results
        $this->table(
            ['User', 'Old Score', 'New Score', 'Old Max', 'New Max', 'Change'],
            $results
        );

        $this->info("Recalculated credit scores for {$users->count()} user(s).");

        return Command::SUCCESS;
    }
}
