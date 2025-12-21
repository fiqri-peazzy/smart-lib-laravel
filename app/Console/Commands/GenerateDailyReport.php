<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:daily
                            {--date= : Specific date (Y-m-d format)}
                            {--save : Save report to file}
                            {--email= : Email report to specific address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate laporan harian aktivitas perpustakaan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date')) : now();

        $this->info("Generating daily report for: " . $date->format('d M Y'));
        $this->newLine();

        // Collect statistics
        $stats = $this->collectStatistics($date);

        // Display report
        $this->displayReport($stats, $date);

        // Save to file if requested
        if ($this->option('save')) {
            $this->saveReport($stats, $date);
        }

        // Email report if requested
        if ($this->option('email')) {
            $this->emailReport($stats, $date, $this->option('email'));
        }

        return Command::SUCCESS;
    }

    /**
     * Collect daily statistics
     */
    protected function collectStatistics(\Carbon\Carbon $date): array
    {
        return [
            // Loans
            'new_loans' => Loan::whereDate('loan_date', $date)->count(),
            'returned_today' => Loan::whereDate('return_date', $date)->count(),
            'active_loans' => Loan::whereIn('status', ['active', 'extended'])->count(),
            'overdue_loans' => Loan::where('status', 'overdue')->count(),

            // Bookings
            'new_bookings' => Booking::whereDate('booking_date', $date)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'expired_bookings' => Booking::whereDate('updated_at', $date)
                ->where('status', 'expired')
                ->count(),

            // Fines
            'new_fines' => Fine::whereDate('created_at', $date)->sum('amount'),
            'fines_paid_today' => Fine::whereDate('paid_at', $date)->sum('paid_amount'),
            'total_unpaid_fines' => Fine::where('status', 'unpaid')->sum('amount'),

            // Users
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'users_with_fines' => User::where('total_fines', '>', 0)->count(),

            // Top borrowers today
            'top_borrowers' => Loan::whereDate('loan_date', $date)
                ->with('user')
                ->get()
                ->groupBy('user_id')
                ->map(fn($loans) => [
                    'user' => $loans->first()->user->name,
                    'count' => $loans->count(),
                ])
                ->sortByDesc('count')
                ->take(5)
                ->values(),
        ];
    }

    /**
     * Display report in console
     */
    protected function displayReport(array $stats, \Carbon\Carbon $date): void
    {
        // Header
        $this->line('═══════════════════════════════════════════════════════');
        $this->line('          LAPORAN HARIAN PERPUSTAKAAN FASILKOM');
        $this->line('                  ' . $date->format('d F Y'));
        $this->line('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Loans Section
        $this->info('📚 PEMINJAMAN');
        $this->line('───────────────────────────────────────────────────────');
        $this->line("  Peminjaman Baru Hari Ini   : {$stats['new_loans']} transaksi");
        $this->line("  Pengembalian Hari Ini      : {$stats['returned_today']} transaksi");
        $this->line("  Total Peminjaman Aktif     : {$stats['active_loans']} transaksi");
        $this->line("  Peminjaman Terlambat       : {$stats['overdue_loans']} transaksi");
        $this->newLine();

        // Bookings Section
        $this->info('📖 BOOKING');
        $this->line('───────────────────────────────────────────────────────');
        $this->line("  Booking Baru Hari Ini      : {$stats['new_bookings']} booking");
        $this->line("  Booking Pending            : {$stats['pending_bookings']} booking");
        $this->line("  Booking Expired Hari Ini   : {$stats['expired_bookings']} booking");
        $this->newLine();

        // Fines Section
        $this->info('💰 DENDA');
        $this->line('───────────────────────────────────────────────────────');
        $this->line("  Denda Baru Hari Ini        : Rp " . number_format($stats['new_fines'], 0, ',', '.'));
        $this->line("  Denda Dibayar Hari Ini     : Rp " . number_format($stats['fines_paid_today'], 0, ',', '.'));
        $this->line("  Total Denda Belum Dibayar  : Rp " . number_format($stats['total_unpaid_fines'], 0, ',', '.'));
        $this->newLine();

        // Users Section
        $this->info('👥 PENGGUNA');
        $this->line('───────────────────────────────────────────────────────');
        $this->line("  Total Pengguna             : {$stats['total_users']} user");
        $this->line("  Pengguna Aktif             : {$stats['active_users']} user");
        $this->line("  Pengguna dengan Denda      : {$stats['users_with_fines']} user");
        $this->newLine();

        // Top Borrowers
        if ($stats['top_borrowers']->isNotEmpty()) {
            $this->info('🏆 TOP BORROWERS HARI INI');
            $this->line('───────────────────────────────────────────────────────');
            foreach ($stats['top_borrowers'] as $index => $borrower) {
                $this->line("  " . ($index + 1) . ". {$borrower['user']} - {$borrower['count']} buku");
            }
            $this->newLine();
        }

        $this->line('═══════════════════════════════════════════════════════');
        $this->info('Report generated at: ' . now()->format('d M Y H:i:s'));
        $this->line('═══════════════════════════════════════════════════════');
    }

    /**
     * Save report to file
     */
    protected function saveReport(array $stats, \Carbon\Carbon $date): void
    {
        $filename = 'reports/daily-' . $date->format('Y-m-d') . '.txt';

        $content = "LAPORAN HARIAN PERPUSTAKAAN FASILKOM\n";
        $content .= $date->format('d F Y') . "\n";
        $content .= str_repeat('=', 60) . "\n\n";

        $content .= "PEMINJAMAN\n";
        $content .= "  Peminjaman Baru: {$stats['new_loans']}\n";
        $content .= "  Pengembalian: {$stats['returned_today']}\n";
        $content .= "  Aktif: {$stats['active_loans']}\n";
        $content .= "  Terlambat: {$stats['overdue_loans']}\n\n";

        $content .= "BOOKING\n";
        $content .= "  Booking Baru: {$stats['new_bookings']}\n";
        $content .= "  Pending: {$stats['pending_bookings']}\n";
        $content .= "  Expired: {$stats['expired_bookings']}\n\n";

        $content .= "DENDA\n";
        $content .= "  Denda Baru: Rp " . number_format($stats['new_fines'], 0) . "\n";
        $content .= "  Dibayar: Rp " . number_format($stats['fines_paid_today'], 0) . "\n";
        $content .= "  Belum Dibayar: Rp " . number_format($stats['total_unpaid_fines'], 0) . "\n\n";

        $content .= "Generated: " . now()->format('d M Y H:i:s') . "\n";

        Storage::put($filename, $content);

        $this->newLine();
        $this->info("✅ Report saved to: storage/app/{$filename}");
    }

    /**
     * Email report
     */
    protected function emailReport(array $stats, \Carbon\Carbon $date, string $email): void
    {
        // TODO: Implement email sending
        // Mail::to($email)->send(new DailyReportMail($stats, $date));

        $this->newLine();
        $this->info("📧 Report would be sent to: {$email}");
        $this->comment("(Email sending not yet implemented)");
    }
}
