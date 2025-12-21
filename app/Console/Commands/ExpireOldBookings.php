<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireOldBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire bookings yang sudah melewati tanggal kadaluarsa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        // Get all pending/notified bookings that are past expires_at date
        $expiredBookings = Booking::whereIn('status', ['pending', 'notified'])
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return Command::SUCCESS;
        }

        $count = 0;
        $bar = $this->output->createProgressBar($expiredBookings->count());
        $bar->start();

        foreach ($expiredBookings as $booking) {
            // Update status to expired
            $booking->expire();

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Expired {$count} booking(s).");

        // Show summary
        $this->table(
            ['User', 'Book', 'Booking Date', 'Expired At', 'Priority'],
            $expiredBookings->map(function ($booking) {
                return [
                    $booking->user->name . ' (' . $booking->user->nim . ')',
                    $booking->book->title,
                    $booking->booking_date->format('d M Y'),
                    $booking->expires_at->format('d M Y'),
                    $booking->is_priority ? 'Yes' : 'No',
                ];
            })
        );

        return Command::SUCCESS;
    }
}
