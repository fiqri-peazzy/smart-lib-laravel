<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     * Ensure only mahasiswa & dosen can access user routes
     * Admin & staff should use Filament
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not authenticated, allow (will be handled by auth middleware)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // If user is admin or staff, redirect to admin panel
        if ($user->hasAnyRole(['admin', 'staff'])) {
            return redirect('/admin')->with('info', 'Silakan gunakan panel admin untuk akses sistem.');
        }

        // Allow mahasiswa & dosen
        if ($user->hasAnyRole(['mahasiswa', 'dosen'])) {
            return $next($request);
        }

        // If user has no role, deny access
        abort(403, 'Akses ditolak. Hubungi administrator untuk verifikasi akun Anda.');
    }
}
