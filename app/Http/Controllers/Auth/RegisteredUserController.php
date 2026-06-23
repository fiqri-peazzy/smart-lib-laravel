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
    ], [
        // NAME
        'name.required'         => 'Nama lengkap wajib diisi.',
        'name.string'           => 'Nama lengkap harus berupa teks.',
        'name.max'              => 'Nama lengkap tidak boleh lebih dari 255 karakter.',

        // ROLE
        'role.required'         => 'Jenis pengguna wajib dipilih.',
        'role.in'               => 'Jenis pengguna yang dipilih tidak valid.',

        // NIM
        'nim.required_if'       => 'NIM wajib diisi untuk mahasiswa.',
        'nim.string'            => 'NIM harus berupa teks.',
        'nim.max'               => 'NIM tidak boleh lebih dari 20 karakter.',
        'nim.unique'            => 'NIM sudah terdaftar, gunakan NIM lain.',

        // NIK
        'nik.required_if'       => 'NIK wajib diisi untuk pengguna umum.',
        'nik.string'            => 'NIK harus berupa teks.',
        'nik.max'               => 'NIK tidak boleh lebih dari 20 karakter.',
        'nik.unique'            => 'NIK sudah terdaftar, gunakan NIK lain.',

        // EMAIL
        'email.required'        => 'Alamat email wajib diisi.',
        'email.string'          => 'Alamat email harus berupa teks.',
        'email.email'           => 'Format alamat email tidak valid.',
        'email.max'             => 'Alamat email tidak boleh lebih dari 255 karakter.',
        'email.unique'          => 'Alamat email sudah terdaftar, gunakan email lain.',

        // MAJOR
        'major_id.required_if'  => 'Program studi wajib dipilih untuk mahasiswa.',
        'major_id.exists'       => 'Program studi yang dipilih tidak ditemukan.',

        // ANGKATAN
        'angkatan.required_if'  => 'Tahun angkatan wajib diisi untuk mahasiswa.',
        'angkatan.integer'      => 'Tahun angkatan harus berupa angka.',
        'angkatan.min'          => 'Tahun angkatan tidak boleh kurang dari 2000.',
        'angkatan.max'          => 'Tahun angkatan tidak boleh melebihi tahun ini ('.date('Y').').',

        // PHONE
        'phone.required'        => 'Nomor telepon wajib diisi.',
        'phone.string'          => 'Nomor telepon harus berupa teks.',
        'phone.max'             => 'Nomor telepon tidak boleh lebih dari 20 karakter.',

        // PASSWORD
        'password.required'     => 'Kata sandi wajib diisi.',
        'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
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

        // Handle NIM/NIK berdasarkan role
        $nim = null;
        $nik = null;

        if ($request->role === 'mahasiswa') {
            $nim = $request->nim;
        } elseif ($request->role === 'umum') {
            $nik = $request->nik;
        }

        $user = User::create([
            'name' => $request->name,
            'nim' => $nim,
            'nik' => $nik,
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
