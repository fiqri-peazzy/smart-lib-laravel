<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Booking;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Display user's loans
     */
    public function loans()
    {
        $user = Auth::user();

        // Pending Pickup (requested but not picked up yet)
        $pendingPickupLoans = $user->loans()
            ->with(['bookItem.book.categories'])
            ->where('status', 'pending_pickup')
            ->latest('requested_at')
            ->get();

        // Active Loans (active + extended)
        $activeLoans = $user->loans()
            ->with(['bookItem.book.categories'])
            ->whereIn('status', ['active', 'extended'])
            ->latest('loan_date')
            ->get();

        // Overdue Loans
        $overdueLoans = $user->loans()
            ->with(['bookItem.book.categories', 'fine'])
            ->where('status', 'overdue')
            ->latest('due_date')
            ->get();

        // History (returned)
        $historyLoans = $user->loans()
            ->with(['bookItem.book.categories'])
            ->where('status', 'returned')
            ->latest('return_date')
            ->paginate(10);

        return view('user.my-loans', compact('pendingPickupLoans', 'activeLoans', 'overdueLoans', 'historyLoans'));
    }

    /**
     * Extend a loan
     */
    public function extendLoan(Loan $loan)
    {
        // Authorization check
        if ($loan->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk perpanjangan ini.');
        }

        // Check if can be extended
        if (!$loan->canBeExtended()) {
            $reason = 'Peminjaman tidak dapat diperpanjang.';

            if ($loan->is_extended) {
                $reason = 'Peminjaman sudah pernah diperpanjang sebelumnya (maksimal 1x).';
            } elseif (!in_array($loan->status, ['active', 'overdue'])) {
                $reason = 'Status peminjaman tidak valid untuk perpanjangan.';
            } else {
                // Check for pending bookings
                $hasPendingBookings = \App\Models\Booking::where('book_id', $loan->bookItem->book_id)
                    ->where('status', 'pending')
                    ->exists();

                if ($hasPendingBookings) {
                    $reason = 'Buku ini sedang dibooking oleh pengguna lain.';
                }
            }

            return back()->with('error', $reason);
        }

        // Extend the loan (default 7 days)
        $extended = $loan->extend(7);

        if ($extended) {
            return back()->with('success', 'Peminjaman berhasil diperpanjang 7 hari.');
        }

        return back()->with('error', 'Gagal memperpanjang peminjaman. Silakan coba lagi.');
    }

    /**
     * Display user's bookings
     */
    public function bookings()
    {
        $user = Auth::user();

        // Pending Bookings
        $pendingBookings = $user->bookings()
            ->with(['book.categories', 'book.items'])
            ->where('status', 'pending')
            ->latest('booking_date')
            ->get();

        // Notified Bookings (book tersedia, user sudah di-notify)
        $notifiedBookings = $user->bookings()
            ->with(['book.categories', 'book.items'])
            ->where('status', 'notified')
            ->latest('notified_at')
            ->get();

        // History (fulfilled, cancelled, expired)
        $historyBookings = $user->bookings()
            ->with(['book.categories'])
            ->whereIn('status', ['fulfilled', 'cancelled', 'expired'])
            ->latest('updated_at')
            ->paginate(10);

        return view('user.my-bookings', compact('pendingBookings', 'notifiedBookings', 'historyBookings'));
    }

    /**
     * Create a new booking
     */
    public function createBooking(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $book = Book::findOrFail($request->book_id);

        // Check if user can borrow
        if (!$user->canBorrow()) {
            return back()->with('error', 'Anda tidak dapat membuat booking. Silakan bayar denda terlebih dahulu.');
        }

        // Check if book is available (shouldn't allow booking if available)
        if ($book->available_stock > 0) {
            return back()->with('error', 'Buku ini masih tersedia. Anda bisa langsung meminjam tanpa booking.');
        }

        // Check if user already has active booking for this book
        $existingBooking = $user->bookings()
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'notified'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Anda sudah memiliki booking aktif untuk buku ini.');
        }

        // Check if user already borrowed this book
        $activeLoan = $user->activeLoans()
            ->whereHas('bookItem', function ($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->first();

        if ($activeLoan) {
            return back()->with('error', 'Anda sedang meminjam buku ini.');
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'booking_date' => now(),
            'expires_at' => now()->addDays(3),
            'status' => 'pending',
            'is_priority' => $user->isDosen(), // Dosen auto priority
        ]);

        return back()->with('success', 'Booking berhasil dibuat. Anda akan diberitahu saat buku tersedia.');
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking)
    {
        // Authorization check
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk membatalkan booking ini.');
        }

        // Check if can be cancelled
        if (!in_array($booking->status, ['pending', 'notified'])) {
            return back()->with('error', 'Booking dengan status "' . $booking->status . '" tidak dapat dibatalkan.');
        }

        // Cancel booking
        $booking->cancel('Dibatalkan oleh user');

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        $user = Auth::user()->load(['major', 'loans']);

        // Get loan history stats
        $loanHistory = $user->loans()->selectRaw('
            COUNT(*) as total_loans,
            SUM(CASE WHEN return_date <= due_date THEN 1 ELSE 0 END) as on_time,
            SUM(CASE WHEN return_date > due_date THEN 1 ELSE 0 END) as late,
            SUM(CASE WHEN status = "overdue" THEN 1 ELSE 0 END) as currently_overdue
        ')->first();

        // Get fines summary
        $finesSummary = $user->fines()->selectRaw('
            SUM(amount) as total_fines,
            SUM(CASE WHEN status = "paid" THEN paid_amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status = "unpaid" THEN amount ELSE 0 END) as total_unpaid
        ')->first();

        return view('user.profile', compact('user', 'loanHistory', 'finesSummary'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'required_with:current_password', 'confirmed', Password::min(8)],
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Handle password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
