<?php

use Illuminate\Support\Facades\Route;
use App\Models\BookItem;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
    return view('app');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/book-items/{bookItem}/print-qr', function (BookItem $bookItem) {
        return view('book-items.print-qr', [
            'item' => $bookItem
        ]);
    })->name('book-items.print-qr');
});
