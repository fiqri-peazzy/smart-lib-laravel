<?php

namespace App\Http\Controllers;

use App\Models\DigitalCollection;
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
        $query = DigitalCollection::with(['major'])->public();

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('major')) {
            $query->where('major_id', $request->major);
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
                $query->orderBy('view_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('download_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $collections = $query->paginate(12)->withQueryString();
        $majors = Major::all();
        $types = DigitalCollection::select('type')->distinct()->pluck('type');

        return view('frontend.digital.index', compact('collections', 'majors', 'types'));
    }

    /**
     * Display the specified digital collection.
     */
    public function show(DigitalCollection $collection)
    {
        $collection->incrementViews();
        
        // Related items
        $relatedItems = DigitalCollection::where('type', $collection->type)
            ->where('id', '!=', $collection->id)
            ->public()
            ->take(4)
            ->get();

        return view('frontend.digital.show', compact('collection', 'relatedItems'));
    }

    /**
     * View/Read the digital file.
     */
    public function read(DigitalCollection $collection)
    {
        // Access check
        if (!$collection->canBeAccessedBy(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membaca file ini secara digital.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($collection->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return view('frontend.digital.read', compact('collection'));
    }

    /**
     * Download the digital file.
     */
    public function download(DigitalCollection $collection)
    {
        // Access check
        if (!$collection->canBeAccessedBy(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($collection->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $collection->incrementDownloads();

        return Storage::disk('public')->download(
            $collection->file_path, 
            $collection->title . '.' . pathinfo($collection->file_path, PATHINFO_EXTENSION)
        );
    }
}
