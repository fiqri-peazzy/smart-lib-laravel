@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Profil Saya</h1>
                    <p class="text-indigo-100">Kelola informasi akun dan preferensi Anda</p>
                </div>
                <a href="{{ route('dashboard') }}"
                    class="hidden md:inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-colors">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-500 rounded-xl flex items-start">
                    <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()"
                        class="text-green-600 hover:text-green-800 dark:text-green-400">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Sidebar - User Info Card -->
                <aside class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-24">
                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    class="w-32 h-32 rounded-2xl mx-auto ring-4 ring-indigo-100 dark:ring-indigo-900/20 shadow-lg">
                                <div
                                    class="absolute bottom-0 right-0 w-10 h-10 bg-green-500 rounded-full border-4 border-white dark:border-gray-800 flex items-center justify-center">
                                    <i class="bi bi-check-lg text-white font-bold"></i>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-4">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->nim }}</p>
                            <div class="mt-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-full">
                                    <i class="bi bi-shield-check mr-1"></i>
                                    {{ $user->role_name }}
                                </span>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="space-y-4 mb-6">
                            <!-- Credit Score -->
                            <div
                                class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Credit Score</span>
                                    <i class="bi bi-star-fill text-yellow-500"></i>
                                </div>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->credit_score }}
                                </div>
                                <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all"
                                        style="width: {{ $user->credit_score }}%"></div>
                                </div>
                            </div>

                            <!-- Max Loans -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bi bi-book text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Max Pinjam</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->max_loans }}
                                            Buku</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Fines -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <i class="bi bi-wallet2 text-red-600 dark:text-red-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Denda Aktif</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">Rp
                                            {{ number_format($user->total_fines ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="space-y-2">
                            <a href="{{ route('loans.my-loans') }}"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex items-center">
                                    <i class="bi bi-book text-gray-600 dark:text-gray-400 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Peminjaman</span>
                                </div>
                                <i class="bi bi-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                            </a>
                            <a href="{{ route('bookings.my-bookings') }}"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex items-center">
                                    <i class="bi bi-calendar-event text-gray-600 dark:text-gray-400 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Booking</span>
                                </div>
                                <i class="bi bi-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                            </a>
                            <a href="{{ route('payment.index') }}"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex items-center">
                                    <i class="bi bi-credit-card text-gray-600 dark:text-gray-400 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pembayaran</span>
                                </div>
                                <i class="bi bi-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Main Content - Edit Profile Form -->
                <main class="lg:col-span-2 space-y-6">
                    <!-- Account Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-person-circle mr-3"></i>
                            Informasi Akun
                        </h2>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Avatar Upload -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-image mr-1"></i>
                                        Foto Profil
                                    </label>
                                    <div class="flex items-center space-x-6">
                                        <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar Preview"
                                            class="w-20 h-20 rounded-xl object-cover ring-2 ring-gray-200 dark:ring-gray-700">
                                        <div class="flex-1">
                                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400"
                                                onchange="previewAvatar(event)">
                                            <p class="mt-1 text-xs text-gray-500">PNG, JPG hingga 2MB</p>
                                        </div>
                                    </div>
                                    @error('avatar')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div>
                                    <label for="name"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-person mr-1"></i>
                                        Nama Lengkap
                                    </label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $user->name) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-envelope mr-1"></i>
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $user->email) }}" required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-phone mr-1"></i>
                                        No. Telepon
                                    </label>
                                    <input type="text" name="phone" id="phone"
                                        value="{{ old('phone', $user->phone) }}"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Read-only Info -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="bi bi-credit-card mr-1"></i>
                                            NIM
                                        </label>
                                        <input type="text" value="{{ $user->nim }}" disabled
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="bi bi-mortarboard mr-1"></i>
                                            Program Studi
                                        </label>
                                        <input type="text" value="{{ $user->major?->name ?? 'N/A' }}" disabled
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg">
                                        <i class="bi bi-check-circle mr-2"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-shield-lock mr-3"></i>
                            Ubah Password
                        </h2>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Current Password -->
                                <div>
                                    <label for="current_password"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-key mr-1"></i>
                                        Password Saat Ini
                                    </label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="new_password"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-lock mr-1"></i>
                                        Password Baru
                                    </label>
                                    <input type="password" name="new_password" id="new_password"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                                    @error('new_password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="new_password_confirmation"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="bi bi-lock-fill mr-1"></i>
                                        Konfirmasi Password Baru
                                    </label>
                                    <input type="password" name="new_password_confirmation"
                                        id="new_password_confirmation"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg">
                                        <i class="bi bi-shield-check mr-2"></i>
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Activity Statistics -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-graph-up mr-3"></i>
                            Statistik Aktivitas
                        </h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Total Loans -->
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                    {{ $loanHistory->total_loans ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Total Pinjam</div>
                            </div>

                            <!-- On Time -->
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">
                                    {{ $loanHistory->on_time ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Tepat Waktu</div>
                            </div>

                            <!-- Late -->
                            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl">
                                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-1">
                                    {{ $loanHistory->late ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Terlambat</div>
                            </div>

                            <!-- Currently Overdue -->
                            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
                                <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-1">
                                    {{ $loanHistory->currently_overdue ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Overdue</div>
                            </div>
                        </div>

                        <!-- Fines Summary -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Ringkasan Denda</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Denda</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">Rp
                                            {{ number_format($finesSummary->total_fines ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <i class="bi bi-wallet2 text-2xl text-gray-400"></i>
                                </div>
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Sudah Dibayar</div>
                                        <div class="text-lg font-bold text-green-600 dark:text-green-400">Rp
                                            {{ number_format($finesSummary->total_paid ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <i class="bi bi-check-circle text-2xl text-green-400"></i>
                                </div>
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Belum Dibayar</div>
                                        <div class="text-lg font-bold text-red-600 dark:text-red-400">Rp
                                            {{ number_format($finesSummary->total_unpaid ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <i class="bi bi-exclamation-circle text-2xl text-red-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Avatar Preview Script -->
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
