<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncUserLoanLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-loan-limits {--role= : Filter by role (mahasiswa, dosen, umum)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi max_loans untuk user berdasarkan role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = User::query();

        if ($this->option('role')) {
            $query->role($this->option('role'));
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('Tidak ada user yang ditemukan.');
            return Command::SUCCESS;
        }

        $updated = 0;

        foreach ($users as $user) {
            $before = $user->max_loans;
            $user->updateMaxLoans();
            $user->refresh();

            if ((int) $user->max_loans !== (int) $before) {
                $updated++;
            }
        }

        $this->info("Sinkronisasi selesai. Diproses {$users->count()} user, nilai max_loans diperbarui untuk {$updated} user.");

        return Command::SUCCESS;
    }
}
