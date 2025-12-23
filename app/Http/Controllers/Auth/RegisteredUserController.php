<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $majors = \App\Models\Major::where('is_active', true)->get();
        return view('auth.register', compact('majors'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'major_id' => ['required', 'exists:majors,id'],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate username from name
        $username = strtolower(str_replace(' ', '.', $request->name));
        $baseUsername = $username;
        $counter = 1;

        // Ensure unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'major_id' => $request->major_id,
            'angkatan' => $request->angkatan,
            'phone' => $request->phone,
            'credit_score' => 100, // Default perfect score
            'max_loans' => 4, // Default max loans
            'status' => 'active',
        ]);

        // Assign mahasiswa role
        $user->assignRole('mahasiswa');

        // Create loan history
        \App\Models\LoanHistory::create([
            'user_id' => $user->id,
            'calculated_score' => 100,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di SIPERPUS.');
    }
}
