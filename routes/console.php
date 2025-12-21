<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ==============================================
// DAILY TASKS
// ==============================================

// Update overdue loans - Run every day at 00:01
Schedule::command('loans:update-overdue')
    ->dailyAt('00:01')
    ->name('Update Overdue Loans')
    ->onSuccess(function () {
        Log::info('Overdue loans updated successfully');
    })
    ->onFailure(function () {
        Log::error('Failed to update overdue loans');
    });

// Expire old bookings - Run every day at 00:05
Schedule::command('bookings:expire')
    ->dailyAt('00:05')
    ->name('Expire Old Bookings')
    ->onSuccess(function () {
        Log::info('Old bookings expired successfully');
    });

// Send loan reminders - Run every day at 08:00 (morning)
Schedule::command('loans:send-reminders')
    ->dailyAt('08:00')
    ->name('Send Loan Reminders')
    ->emailOutputOnFailure('admin@ichsan.ac.id'); // Optional: email on failure

// Generate daily report - Run every day at 23:00
Schedule::command('reports:daily --save')
    ->dailyAt('23:00')
    ->name('Generate Daily Report')
    ->onSuccess(function () {
        Log::info('Daily report generated successfully');
    });

// ==============================================
// WEEKLY TASKS
// ==============================================

// Recalculate all credit scores - Every Sunday at 01:00
Schedule::command('users:recalculate-credit-scores')
    ->weeklyOn(0, '01:00') // 0 = Sunday
    ->name('Weekly Credit Score Recalculation')
    ->onSuccess(function () {
        Log::info('Weekly credit score recalculation completed');
    });

// ==============================================
// MONTHLY TASKS
// ==============================================

// Generate monthly report - First day of month at 02:00
Schedule::command('reports:daily --save --date=' . now()->subMonth()->endOfMonth()->format('Y-m-d'))
    ->monthlyOn(1, '02:00')
    ->name('Monthly Report Generation');

// ==============================================
// HOURLY TASKS (Optional)
// ==============================================

// Update overdue status every hour (for real-time updates)
Schedule::command('loans:update-overdue')
    ->hourly()
    ->name('Hourly Overdue Check')
    ->between('08:00', '20:00') // Only during library hours
    ->withoutOverlapping(); // Prevent overlapping executions

// ==============================================
// CUSTOM INTERVALS
// ==============================================

// Send reminders multiple times a day
Schedule::command('loans:send-reminders')
    ->twiceDaily(8, 16) // 08:00 and 16:00
    ->name('Reminders Twice Daily');

// ==============================================
// MAINTENANCE TASKS
// ==============================================

// Clean up old logs - Every week
Schedule::command('log:clear')
    ->weekly()
    ->name('Clean Old Logs');

// Database backup - Every day at 03:00
Schedule::command('backup:run')
    ->dailyAt('03:00')
    ->name('Daily Database Backup');

// ==============================================
// EXAMPLE: Conditional Scheduling
// ==============================================

// Only run on weekdays (Monday-Friday)
Schedule::command('loans:send-reminders')
    ->weekdays()
    ->at('09:00')
    ->name('Weekday Reminders');

// Skip on holidays (you need to define holiday logic)
Schedule::command('loans:update-overdue')
    ->daily()
    ->skip(function () {
        // Check if today is a holiday
        // return Holiday::isToday();
        return false;
    });
