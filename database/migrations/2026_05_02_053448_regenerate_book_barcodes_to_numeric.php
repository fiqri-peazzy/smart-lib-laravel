<?php

use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Regenerasi semua barcode buku ke format 16 karakter alphanumeric random
     * untuk kompatibilitas QR code scanner.
     */
    public function up(): void
    {
        $used = [];

        Book::withTrashed()->orderBy('id')->each(function (Book $book) use (&$used) {
            do {
                $code = strtoupper(Str::random(16));
            } while (in_array($code, $used));

            $used[] = $code;

            $book->withoutEvents(function () use ($book, $code) {
                $book->barcode = $code;
                $book->saveQuietly();
            });
        });
    }

    /**
     * Irreversible — backup database sebelum jalankan.
     */
    public function down(): void
    {
        //
    }
};
