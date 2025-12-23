<x-guest-layout>
    <div
        class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="w-full max-w-2xl">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white text-center">
                    <div
                        class="w-16 h-16 bg-white/20 backdrop-blur-lg rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-2">Registrasi Anggota</h2>
                    <p class="text-indigo-100">SIPERPUS Fasilkom Ichsan</p>
                </div>

                <!-- Form -->
                <div class="p-8">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <x-input-label for="name" value="Nama Lengkap *" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name')" required autofocus autocomplete="name"
                                    placeholder="Contoh: John Doe" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- NIM -->
                            <div>
                                <x-input-label for="nim" value="NIM *" />
                                <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim"
                                    :value="old('nim')" required placeholder="Contoh: 2021310001" />
                                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" value="Email *" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email')" required autocomplete="username"
                                    placeholder="nama@student.ichsan.ac.id" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Major -->
                            <div>
                                <x-input-label for="major_id" value="Jurusan/Prodi *" />
                                <select id="major_id" name="major_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Pilih Prodi</option>
                                    @foreach ($majors as $major)
                                        <option value="{{ $major->id }}"
                                            {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                            {{ $major->code }} - {{ $major->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('major_id')" class="mt-2" />
                            </div>

                            <!-- Angkatan -->
                            <div>
                                <x-input-label for="angkatan" value="Tahun Angkatan *" />
                                <x-text-input id="angkatan" class="block mt-1 w-full" type="number" name="angkatan"
                                    :value="old('angkatan', date('Y'))" required min="2000" :max="date('Y')"
                                    placeholder="{{ date('Y') }}" />
                                <x-input-error :messages="$errors->get('angkatan')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div class="md:col-span-2">
                                <x-input-label for="phone" value="No. Telepon/WhatsApp *" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                    :value="old('phone')" required placeholder="Contoh: 081234567890" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Untuk notifikasi reminder peminjaman
                                </p>
                            </div>

                            <!-- Password -->
                            <div>
                                <x-input-label for="password" value="Password *" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                    required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-input-label for="password_confirmation" value="Konfirmasi Password *" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Ulangi password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div
                            class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg">
                            <h4 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-2">ℹ️ Informasi Penting:
                            </h4>
                            <ul class="text-sm text-indigo-800 dark:text-indigo-400 space-y-1 list-disc list-inside">
                                <li>Username akan otomatis dibuat dari nama Anda</li>
                                <li>Credit score awal: 100 (Perfect Score)</li>
                                <li>Maksimal peminjaman: 4 buku</li>
                                <li>Akun akan langsung aktif setelah registrasi</li>
                            </ul>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start">
                            <input type="checkbox" id="terms" name="terms" required
                                class="mt-1 rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="terms" class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                                Saya setuju dengan
                                <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">syarat
                                    dan ketentuan</a>
                                perpustakaan
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:-translate-y-0.5 transition-all">
                                Daftar Sekarang
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Sudah punya akun?
                                <a href="{{ route('login') }}"
                                    class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-6">
                <a href="{{ route('home') }}"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                    ← Kembali ke Homepage
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
