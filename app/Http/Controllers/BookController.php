<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    /**
     * Browse books with filters
     */
    public function index(Request $request)
    {
        $query = Book::with(['categories', 'recommendedForMajor', 'items']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('book_categories.id', $request->category);
            });
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('publication_year', $request->year);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'author':
                $query->orderBy('author', 'asc');
                break;
            case 'popular':
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
        }

        // Paginate
        $books = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = BookCategory::withCount('books')
            ->orderBy('name')
            ->get();

        // Get years for filter
        $years = Book::selectRaw('DISTINCT publication_year')
            ->whereNotNull('publication_year')
            ->orderBy('publication_year', 'desc')
            ->pluck('publication_year');

        return view('frontend.books.index', compact('books', 'categories', 'years'));
    }

    /**
     * Show book detail
     */
    public function show(Book $book)
    {
        $book->load(['categories', 'recommendedForMajor', 'items' => function ($query) {
            $query->where('status', 'available');
        }]);

        // Check if user can borrow
        $canBorrow = false;
        $borrowMessage = '';
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->canBorrow()) {
                $borrowMessage = 'Anda tidak dapat meminjam. Silakan bayar denda atau hubungi admin.';
            } elseif ($user->activeLoans()->count() >= $user->max_loans) {
                $borrowMessage = "Anda sudah mencapai limit peminjaman ({$user->max_loans} buku).";
            } elseif ($book->available_stock <= 0) {
                $borrowMessage = 'Semua eksemplar sedang dipinjam. Anda dapat booking buku ini.';
            } else {
                $canBorrow = true;
            }
        } else {
            $borrowMessage = 'Silakan login untuk meminjam buku.';
        }

        // Related books (same category)
        $relatedBooks = Book::whereHas('categories', function ($q) use ($book) {
            $q->whereIn('book_categories.id', $book->categories->pluck('id'));
        })
            ->where('id', '!=', $book->id)
            ->where('is_available', true)
            ->take(4)
            ->get();

        // Check if already booked by user
        $hasBooking = false;
        if (Auth::check()) {
            $hasBooking = \App\Models\Booking::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->where('status', 'pending')
                ->exists();
        }

        return view('frontend.books.show', compact(
            'book',
            'canBorrow',
            'borrowMessage',
            'relatedBooks',
            'hasBooking'
        ));
    }

    /**
     * Books by category
     */
    public function byCategory(BookCategory $category)
    {
        $books = Book::whereHas('categories', function ($q) use ($category) {
            $q->where('book_categories.id', $category->id);
        })
            ->where('is_available', true)
            ->with(['categories', 'items'])
            ->paginate(12);

        return view('frontend.books.category', compact('books', 'category'));
    }

    /**
     * Request loan (user action from book detail)
     */
    public function requestLoan(Request $request, Book $book)
    {
        // Must be authenticated
        if (!Auth::check()) {
            return back()->with('error', 'Silakan login terlebih dahulu untuk meminjam buku.');
        }

        $user = Auth::user();

        // Validate user can borrow
        if (!$user->canBorrow()) {
            return back()->with('error', 'Anda tidak dapat meminjam buku. Silakan bayar denda terlebih dahulu.');
        }

        // Check loan limit
        if ($user->activeLoans()->count() >= $user->max_loans) {
            return back()->with('error', "Anda sudah mencapai limit peminjaman ({$user->max_loans} buku).");
        }

        // Check if book is available
        if ($book->available_stock <= 0) {
            return back()->with('error', 'Buku ini tidak tersedia. Silakan lakukan booking.');
        }

        // Check if user already has active loan for this book
        $existingLoan = $user->loans()
            ->whereHas('bookItem', function ($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->whereIn('status', ['pending_pickup', 'active', 'extended', 'overdue'])
            ->first();

        if ($existingLoan) {
            return back()->with('error', 'Anda sudah memiliki peminjaman aktif atau pending untuk buku ini.');
        }

        // Check if user has pending booking for this book
        $existingBooking = $user->bookings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'notified'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Anda sudah memiliki booking untuk buku ini. Silakan ambil buku di perpustakaan.');
        }

        // Use DB transaction
        DB::beginTransaction();
        try {
            // Auto-assign first available book item
            $bookItem = $book->getAvailableItems()->first();

            if (!$bookItem) {
                DB::rollBack();
                return back()->with('error', 'Tidak ada eksemplar yang tersedia saat ini.');
            }

            // Create loan request with pending_pickup status
            $loan = Loan::create([
                'user_id' => $user->id,
                'book_item_id' => $bookItem->id,
                'status' => 'pending_pickup',
                'requested_at' => now(),
                'pickup_deadline' => now()->addDays(3), // 3 days to pickup
                'loan_date' => null, // Will be set when staff confirms pickup
                'due_date' => null, // Will be set when staff confirms pickup
            ]);

            // Book item status is updated in model boot (markAsBorrowed)

            DB::commit();

            return redirect()->route('loans.my-loans')->with(
                'success',
                'Request peminjaman berhasil! Silakan datang ke perpustakaan dalam 3 hari untuk mengambil buku. Tunjukkan QR code Anda ke staff.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Request loan failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}
