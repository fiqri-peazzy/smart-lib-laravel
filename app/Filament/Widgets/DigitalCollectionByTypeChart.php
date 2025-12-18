<?php


namespace App\Filament\Widgets;

use App\Models\DigitalCollection;
use Filament\Widgets\ChartWidget;

class DigitalCollectionByTypeChart extends ChartWidget
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
