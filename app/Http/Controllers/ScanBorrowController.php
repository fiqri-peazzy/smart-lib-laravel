<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanBorrowController extends Controller
{
    public function index()
    {
        return view('scan-borrow.index');
    }

    public function fetchBookDetails(Request $request)
    {
        $qrCode = trim($request->get('barcode')); // barcode parameter from frontend, actually QR Code
        $bookItem = BookItem::with('book.rack')->whereRaw('LOWER(qr_code) = LOWER(?)', [$qrCode])->first();

        if (! $bookItem) {
            return response()->json(['success' => false, 'message' => 'Eksemplar buku tidak ditemukan berdasarkan QR Code tersebut.']);
        }

        if ($bookItem->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'Eksemplar buku ini sedang tidak tersedia (Status: ' . $bookItem->status . ').']);
        }

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $bookItem->book->id,
                'title' => $bookItem->book->title,
                'author' => $bookItem->book->author,
                'barcode' => $bookItem->qr_code,
                'available_stock' => $bookItem->book->available_stock,
                'rack_location' => $bookItem->book->rack?->name ?? '-',
                'cover_url' => $bookItem->book->cover_url,
            ],
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string', // this is actually qr_code
        ]);

        $bookItem = BookItem::with('book')->where('qr_code', $request->barcode)->first();

        if (! $bookItem) {
            return response()->json(['success' => false, 'message' => 'Eksemplar buku tidak ditemukan.']);
        }

        if ($bookItem->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'Eksemplar buku ini sedang tidak tersedia.']);
        }

        $user = Auth::user();

        // Prevent borrowing if user reached max loans
        if ($user->active_loans_count >= $user->max_loans) {
            return response()->json(['success' => false, 'message' => 'Anda telah mencapai batas maksimal peminjaman.']);
        }

        // Create pending_pickup loan
        $loan = Loan::create([
            'user_id' => $user->id,
            'book_item_id' => $bookItem->id,
            'status' => 'pending_pickup',
            'pickup_deadline' => now()->addHours(24),
            'notes' => 'Self-service borrow request',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dibuat. Silakan temui staf perpustakaan untuk proses pengambilan buku.',
            'book' => [
                'title' => $bookItem->book->title,
                'barcode' => $bookItem->qr_code,
                'quantity' => 1,
            ],
        ]);
    }
}
