<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Image -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-900 to-primary-800 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div
                    class="absolute top-20 left-20 w-72 h-72 bg-secondary-400 rounded-full mix-blend-multiply filter blur-xl animate-blob">
                </div>
                <div
                    class="absolute bottom-20 right-20 w-72 h-72 bg-primary-400 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000">
                </div>
            </div>

            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <div class="mb-8 drop-shadow-2xl">
                    <img src="{{ asset('images/logofikom2.png') }}" class="h-32 w-auto" alt="Logo FIKOM">
                </div>
                <h1 class="text-4xl font-black mb-2 uppercase tracking-wide text-center">Fakultas Ilmu Komputer</h1>
                <p class="text-xl text-center mb-8 text-secondary-400 font-medium tracking-wider">Universitas Ichsan
                    Gorontalo</p>
                {{-- <div class="grid grid-cols-3 gap-8 w-full max-w-md">
                    <div class="text-center">
                        <div class="text-3xl font-bold">1000+</div>
                        <div class="text-sm opacity-80">Buku</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">500+</div>
                        <div class="text-sm opacity-80">E-Books</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="text-sm opacity-80">Akses</div>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white dark:bg-gray-900">
            <div class="w-full max-w-md">
                <!-- Logo Mobile -->
                <div class="lg:hidden flex items-center justify-center mb-8">
                    <img src="{{ asset('images/logofikom2.png') }}" class="h-20 w-auto" alt="Logo FIKOM">
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-primary-900 dark:text-white mb-2">Selamat Datang!</h2>
                    <p class="text-gray-600 dark:text-gray-400">Silakan masuk untuk mengakses perpustakaan digital.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Admin Info -->
                <div
                    class="mb-6 p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <p class="text-sm text-primary-800 dark:text-primary-300">
                        <strong>Admin/Staf?</strong> Silakan akses
                        <a href="/admin" class="font-semibold underline hover:text-primary-600">Panel Admin</a>
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Login Field (NIM/Username/Email) -->
                    <div>
                        <x-input-label for="login" value="NIM / Nama Pengguna / Email" />
                        <x-text-input id="login"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="text" name="login" :value="old('login')" required autofocus autocomplete="username"
                            placeholder="Masukkan NIM, username, atau email" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Kata Sandi" />
                        <x-text-input id="password"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-800"
                                name="remember">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 hover:underline"
                                href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:-translate-y-0.5 transition-all">
                            Masuk
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Belum punya akun?
                            <a href="{{ route('register') }}"
                                class="font-bold text-secondary-600 dark:text-secondary-400 hover:text-secondary-700 hover:underline">
                                Daftar sekarang
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Quick Login Test (Development Only - Remove in Production) -->
                @if (config('app.debug'))
                    <div
                        class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-xs text-yellow-800 dark:text-yellow-300 mb-2 font-semibold">⚠️ Development Mode -
                            Quick Login:</p>
                        <div class="flex gap-2 text-xs">
                            <button
                                onclick="document.getElementById('login').value='john.doe@student.ichsan.ac.id'; document.getElementById('password').value='password';"
                                class="px-2 py-1 bg-yellow-200 dark:bg-yellow-800 rounded">
                                Mahasiswa
                            </button>
                            <button
                                onclick="document.getElementById('login').value='dosen@ichsan.ac.id'; document.getElementById('password').value='password';"
                                class="px-2 py-1 bg-yellow-200 dark:bg-yellow-800 rounded">
                                Dosen
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</x-guest-layout>
