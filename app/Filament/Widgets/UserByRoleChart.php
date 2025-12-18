<?php


namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserByRoleChart extends ChartWidget
{
    protected static ?string $heading = 'Pengguna Berdasarkan Role';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $mahasiswa = User::role('mahasiswa')->count();
        $dosen = User::role('dosen')->count();
        $staff = User::role('staff')->count();
        $admin = User::role('admin')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengguna',
                    'data' => [$mahasiswa, $dosen, $staff, $admin],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',  // Blue untuk mahasiswa
                        'rgb(34, 197, 94)',   // Green untuk dosen
                        'rgb(251, 146, 60)',  // Orange untuk staff
                        'rgb(239, 68, 68)',   // Red untuk admin
                    ],
                ],
            ],
            'labels' => ['Mahasiswa', 'Dosen', 'Staff', 'Admin'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
