<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DigitalLibraryController extends Controller
{
    /**
     * Display a listing of the digital collection.
     */
    public function index(Request $request)
    {
        $query = Book::with(['recommendedForMajor', 'authorMaster'])->digital();

        // Filters
        if ($request->filled('type')) {
            $query->where('digital_file_type', $request->type);
        }

        if ($request->filled('major')) {
            $query->where('recommended_for_major_id', $request->major);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'popular':
                $query->orderBy('digital_view_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('digital_download_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $collections = $query->paginate(12)->withQueryString();
        $majors = Major::all();
        $types = Book::digital()->select('digital_file_type as type')->distinct()->pluck('type');

        return view('frontend.digital.index', compact('collections', 'majors', 'types'));
    }

    /**
     * Display the specified digital collection.
     */
    public function show(Book $book)
    {
        if (!$book->is_digital) {
            return redirect()->route('books.show', $book);
        }

        $book->increment('digital_view_count');
        
        // Related items
        $relatedItems = Book::digital()
            ->where('digital_file_type', $book->digital_file_type)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('frontend.digital.show', compact('book', 'relatedItems'));
    }

    /**
     * View/Read the digital file.
     */
    public function read(Book $book)
    {
        if (!$book->is_digital) {
            return back()->with('error', 'Koleksi ini bukan merupakan koleksi digital.');
        }

        // Access check
        if (!$book->canBeAccessedBy(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membaca file ini secara digital.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($book->digital_file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return view('frontend.digital.read', compact('book'));
    }

    /**
     * Download the digital file.
     */
    public function download(Book $book)
    {
        if (!$book->is_digital) {
            return back()->with('error', 'Koleksi ini bukan merupakan koleksi digital.');
        }

        // Access check
        if (!$book->canBeAccessedBy(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($book->digital_file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $book->increment('digital_download_count');

        return Storage::disk('public')->download(
            $book->digital_file_path, 
            $book->title . '.' . pathinfo($book->digital_file_path, PATHINFO_EXTENSION)
        );
    }

}

