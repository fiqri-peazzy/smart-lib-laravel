<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        $barcode = trim($request->get('barcode'));
        $book = Book::whereRaw('LOWER(barcode) = LOWER(?)', [$barcode])->first();

        if (! $book) {
            return response()->json(['success' => false, 'message' => 'Buku tidak ditemukan berdasarkan barcode tersebut.']);
        }

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'barcode' => $book->barcode,
                'available_stock' => $book->available_stock,
                'rack_location' => $book->rack_location,
                'cover_url' => $book->cover_url,
            ],
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $book = Book::where('barcode', $request->barcode)->first();

        if (! $book) {
            return response()->json(['success' => false, 'message' => 'Buku tidak ditemukan.']);
        }

        if (! $book->canBeBorrowed($request->quantity)) {
            return response()->json(['success' => false, 'message' => 'Stok buku tidak mencukupi. Sisa stok tersedia: '.$book->available_stock]);
        }

        $user = Auth::user();

        // Prevent borrowing if user reached max loans
        if ($user->active_loans_count >= $user->max_loans) {
            return response()->json(['success' => false, 'message' => 'Anda telah mencapai batas maksimal peminjaman.']);
        }

        // Create pending_pickup loan
        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'quantity' => $request->quantity,
            'status' => 'pending_pickup',
            'pickup_deadline' => now()->addHours(24),
            'notes' => 'Self-service borrow request',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dibuat. Silakan temui staf perpustakaan untuk proses pengambilan buku.',
            'book' => [
                'title' => $book->title,
                'barcode' => $book->barcode,
                'quantity' => $request->quantity,
            ],
        ]);
    }
}
