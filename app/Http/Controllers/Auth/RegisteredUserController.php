<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'role' => ['required', 'in:mahasiswa,dosen,umum'],
            'nim' => ['required_if:role,mahasiswa', 'nullable', 'string', 'max:20', 'unique:'.User::class],
            'nik' => ['required_if:role,umum', 'nullable', 'string', 'max:20', 'unique:'.User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'major_id' => ['required_if:role,mahasiswa', 'nullable', 'exists:majors,id'],
            'angkatan' => ['required_if:role,mahasiswa', 'nullable', 'integer', 'min:2000', 'max:'.date('Y')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate username from name
        $username = strtolower(str_replace(' ', '.', $request->name));
        $baseUsername = $username;
        $counter = 1;

        // Ensure unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.$counter;
            $counter++;
        }

        // Use NIK as NIM for umum role
        $nim = null;
        if ($request->role === 'mahasiswa') {
            $nim = $request->nim;
        } elseif ($request->role === 'umum') {
            $nim = $request->nik;
        }

        $user = User::create([
            'name' => $request->name,
            'nim' => $nim,
            'nik' => $request->role === 'umum' ? $request->nik : null,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'major_id' => $request->role === 'mahasiswa' ? $request->major_id : null,
            'angkatan' => $request->role === 'mahasiswa' ? $request->angkatan : null,
            'phone' => $request->phone,
            'credit_score' => 100, // Default perfect score
            'status' => 'active',
        ]);

        // Assign role selected during registration
        $user->assignRole($request->role);

        // Update max loans based on role
        $user->updateMaxLoans();

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
