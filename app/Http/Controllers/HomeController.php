<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\DigitalCollection;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Statistics
        $stats = [
            'total_books' => Book::count(),
            'total_digital' => DigitalCollection::count(),
            'available_books' => Book::where('is_available', true)
                ->where('available_stock', '>', 0)
                ->count(),
        ];

        // Popular Categories (with book count)
        $popularCategories = BookCategory::withCount('books')
            ->orderBy('books_count', 'desc')
            ->take(6)
            ->get();

        // Featured Books (is_featured = true)
        $featuredBooks = Book::with(['categories'])
            ->where('is_featured', true)
            ->where('is_available', true)
            ->take(6)
            ->get();

        // Latest Digital Collections
        $latestDigital = DigitalCollection::where('is_public', true)
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.home', compact(
            'stats',
            'popularCategories',
            'featuredBooks',
            'latestDigital'
        ));
    }

    /**
     * Display about page
     */
    public function about()
    {
        return view('frontend.about');
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('frontend.contact');
    }
}
