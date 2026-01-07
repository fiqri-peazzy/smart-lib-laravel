<?php

namespace App\Filament\Widgets;

use App\Models\DigitalCollection;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalMahasiswa = User::role('mahasiswa')->count();
        $totalDosen = User::role('dosen')->count();
        $activeUsers = User::where('status', 'active')->count();

        $totalDigitalCollections = DigitalCollection::count();
        $totalSkripsi = DigitalCollection::where('type', 'skripsi')->count();
        $totalEbooks = DigitalCollection::where('type', 'ebook')->count();

        $usersWithFines = User::where('total_fines', '>', 0)->count();
        $totalFines = User::sum('total_fines');

        // Add loan stats
        $activeLoans = \App\Models\Loan::whereIn('status', ['active', 'overdue', 'extended'])->count();
        $overdueLoans = \App\Models\Loan::where('status', 'overdue')->count();
        $unpaidFines = \App\Models\Fine::where('status', 'unpaid')->sum('amount');

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description("$activeUsers aktif, $totalMahasiswa mahasiswa, $totalDosen dosen")
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->chart([7, 12, 15, 18, 20, 22, $totalUsers]),

            Stat::make('Koleksi Digital', $totalDigitalCollections)
                ->description("$totalEbooks e-book, $totalSkripsi skripsi")
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info')
                ->chart([10, 15, 20, 25, 30, 35, $totalDigitalCollections]),

            // Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($totalFines, 0, ',', '.'))
            //     ->description("$usersWithFines pengguna memiliki denda")
            //     ->descriptionIcon('heroicon-o-banknotes')
            //     ->color($totalFines > 0 ? 'danger' : 'success'),

            Stat::make('Peminjaman Aktif', $activeLoans)
                ->description("$overdueLoans terlambat")
                ->descriptionIcon('heroicon-o-clock')
                ->color($overdueLoans > 0 ? 'danger' : 'success'),

            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($unpaidFines, 0, ',', '.'))
                ->description("$usersWithFines pengguna memiliki denda")
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($unpaidFines > 0 ? 'danger' : 'success'),
        ];
    }
}
