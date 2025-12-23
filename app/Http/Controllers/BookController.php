<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Browse books with filters
     */
    public function index(Request $request)
    {
        $query = Book::with(['categories', 'recommendedForMajor', 'items'])
            ->where('is_available', true);

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
}
