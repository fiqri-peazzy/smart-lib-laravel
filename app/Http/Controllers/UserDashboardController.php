<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil statistik user
        $stats = [
            'active_loans' => $user->activeLoans()->count(),
            'total_loans' => $user->loans()->count(),
            'active_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'total_fines' => $user->total_fines,
        ];

        // Ambil peminjaman terbaru
        $recentLoans = $user->loans()
            ->with('bookItem.book')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('user', 'stats', 'recentLoans'));
    }
}
