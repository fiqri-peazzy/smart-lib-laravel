<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;

class DigitalCollectionsByTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Koleksi Digital Berdasarkan Tipe';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ebook = Book::digital()->where('digital_file_type', 'ebook')->count();
        $jurnal = Book::digital()->where('digital_file_type', 'jurnal')->count();
        $skripsi = Book::digital()->where('digital_file_type', 'skripsi')->count();
        $modul = Book::digital()->where('digital_file_type', 'modul')->count();
        $paper = Book::digital()->where('digital_file_type', 'paper')->count();


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
        return 'line';
    }
}
