<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScanBorrowController extends Controller
{
    public function index()
    {
        return view('scan-borrow.index');
    }

    /**
     * Fetch book details by QR code.
     * Sekarang juga mengembalikan available_stock dan book_id
     * agar frontend bisa menampilkan input jumlah eksemplar.
     */
    public function fetchBookDetails(Request $request)
    {
        $qrCode = trim($request->get('barcode'));

        $bookItem = BookItem::with('book.rack')
            ->whereRaw('LOWER(qr_code) = LOWER(?)', [$qrCode])
            ->first();

        if (! $bookItem) {
            return response()->json([
                'success' => false,
                'message' => 'Eksemplar buku tidak ditemukan berdasarkan QR Code tersebut.',
            ]);
        }

        if ($bookItem->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Eksemplar buku ini sedang tidak tersedia (Status: ' . $bookItem->status . ').',
            ]);
        }

        // Hitung stok available untuk judul yang sama
        $availableStock = BookItem::where('book_id', $bookItem->book_id)
            ->where('status', 'available')
            ->count();

        return response()->json([
            'success' => true,
            'book' => [
                'id'              => $bookItem->book->id,
                'title'           => $bookItem->book->title,
                'author'          => $bookItem->book->author,
                'barcode'         => $bookItem->qr_code,
                'available_stock' => $availableStock,
                'rack_location'   => $bookItem->book->rack?->name ?? '-',
                'cover_url'       => $bookItem->book->cover_url,
            ],
        ]);
    }

    /**
     * Proses peminjaman bulk.
     * User scan 1 QR → input jumlah → sistem auto-assign eksemplar available.
     */
    public function process(Request $request)
    {
        $request->validate([
            'barcode'  => 'required|string',
            'quantity' => 'sometimes|integer|min:1|max:100',
        ]);

        $quantity = (int) ($request->quantity ?? 1);

        // Cari buku berdasarkan QR code yang di-scan
        $scannedItem = BookItem::with('book')
            ->whereRaw('LOWER(qr_code) = LOWER(?)', [trim($request->barcode)])
            ->first();

        if (! $scannedItem) {
            return response()->json([
                'success' => false,
                'message' => 'Eksemplar buku tidak ditemukan.',
            ]);
        }

        $user = Auth::user();

        // Hitung slot peminjaman yang tersisa untuk user ini
        $currentLoans = $user->loans()
            ->whereIn('status', ['active', 'overdue', 'extended', 'pending_pickup'])
            ->count();

        $remainingSlots = $user->max_loans - $currentLoans;

        if ($remainingSlots <= 0) {
            return response()->json([
                'success' => false,
                'message' => "Anda telah mencapai batas maksimal peminjaman ({$user->max_loans} buku).",
            ]);
        }

        // Batasi quantity sesuai sisa slot user
        $canBorrow = min($quantity, $remainingSlots);

        // Ambil ID eksemplar yang sudah dipinjam user (hindari duplikat)
        $alreadyBorrowedItemIds = $user->loans()
            ->whereIn('status', ['active', 'overdue', 'extended', 'pending_pickup'])
            ->pluck('book_item_id')
            ->toArray();

        // Ambil eksemplar available dari judul yang sama, skip yang sudah dipinjam user
        $availableItems = BookItem::where('book_id', $scannedItem->book_id)
            ->where('status', 'available')
            ->whereNotIn('id', $alreadyBorrowedItemIds)
            ->lockForUpdate() // hindari race condition
            ->limit($canBorrow)
            ->get();

        if ($availableItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada eksemplar tersedia untuk judul buku ini.',
            ]);
        }

        $createdLoans  = [];
        $createdCount  = 0;

        DB::transaction(function () use ($user, $availableItems, &$createdLoans, &$createdCount) {
            foreach ($availableItems as $item) {
                Loan::create([
                    'user_id'         => $user->id,
                    'book_item_id'    => $item->id,
                    'status'          => 'pending_pickup',
                    'pickup_deadline' => now()->addHours(24),
                    'notes'           => 'Self-service bulk borrow request',
                ]);

                $createdLoans[] = $item->qr_code;
                $createdCount++;
            }
        });

        $bookTitle      = $scannedItem->book->title;
        $requestedMore  = $quantity > $createdCount;
        $skippedCount   = $quantity - $createdCount;

        // Susun pesan respons
        $message = "Berhasil membuat {$createdCount} permintaan peminjaman untuk buku \"{$bookTitle}\". "
            . "Silakan temui staf perpustakaan untuk proses pengambilan.";

        if ($requestedMore) {
            $reason = ($skippedCount > ($quantity - min($quantity, $remainingSlots)))
                ? 'stok tidak mencukupi'
                : 'batas peminjaman Anda';

            $message .= " {$skippedCount} eksemplar tidak diproses karena {$reason}.";
        }

        return response()->json([
            'success'        => true,
            'message'        => $message,
            'processed'      => $createdCount,
            'requested'      => $quantity,
            'skipped'        => $skippedCount,
            'assigned_items' => $createdLoans, // list QR code yang di-assign
            'book'           => [
                'title'    => $bookTitle,
                'barcode'  => $scannedItem->qr_code,
                'quantity' => $createdCount,
            ],
        ]);
    }
}