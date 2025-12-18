<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class FilamentAdminAuth
{
    /**
     * Handle an incoming request.
     * Hanya admin & staff yang boleh akses Filament Admin Panel
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // Cek apakah user memiliki role admin atau staff
        if (!$user->hasAnyRole(['admin', 'staff'])) {
            abort(403, 'Unauthorized. Hanya Admin dan Staff yang dapat mengakses panel ini.');
        }

        return $next($request);
    }
}
