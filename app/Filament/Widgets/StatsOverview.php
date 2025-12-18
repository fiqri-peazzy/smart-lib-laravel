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

            Stat::make('Denda Belum Dibayar', 'Rp ' . number_format($totalFines, 0, ',', '.'))
                ->description("$usersWithFines pengguna memiliki denda")
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($totalFines > 0 ? 'danger' : 'success'),
        ];
    }
}

// File: app/Filament/Widgets/DigitalCollectionsByTypeChart.php

namespace App\Filament\Widgets;

use App\Models\DigitalCollection;
use Filament\Widgets\ChartWidget;

class DigitalCollectionsByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Koleksi Digital Berdasarkan Tipe';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ebook = DigitalCollection::where('type', 'ebook')->count();
        $jurnal = DigitalCollection::where('type', 'jurnal')->count();
        $skripsi = DigitalCollection::where('type', 'skripsi')->count();
        $modul = DigitalCollection::where('type', 'modul')->count();
        $paper = DigitalCollection::where('type', 'paper')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dokumen',
                    'data' => [$ebook, $jurnal, $skripsi, $modul, $paper],
                    'backgroundColor' => [
                        'rgb(99, 102, 241)',   // Indigo
                        'rgb(34, 197, 94)',    // Green
                        'rgb(251, 191, 36)',   // Yellow
                        'rgb(59, 130, 246)',   // Blue
                        'rgb(156, 163, 175)',  // Gray
                    ],
                ],
            ],
            'labels' => ['E-Book', 'Jurnal', 'Skripsi', 'Modul', 'Paper'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
