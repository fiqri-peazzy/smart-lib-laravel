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
                <div class="text-center text-secondary-300 text-sm leading-relaxed max-w-sm">
                    <p class="mb-4">Bergabunglah dengan komunitas pengguna perpustakaan digital kami</p>
                    <p>Akses ribuan koleksi buku dan materi pembelajaran kapan saja, di mana saja</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white dark:bg-gray-900 overflow-y-auto">
            <div class="w-full max-w-md py-8">
                <!-- Logo Mobile -->
                <div class="lg:hidden flex items-center justify-center mb-8">
                    <img src="{{ asset('images/logofikom2.png') }}" class="h-20 w-auto" alt="Logo FIKOM">
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-primary-900 dark:text-white mb-2">Daftar Sekarang</h2>
                    <p class="text-gray-600 dark:text-gray-400">Buat akun untuk mengakses perpustakaan digital.</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm text-red-800 dark:text-red-300 font-semibold mb-2">Kesalahan validasi:</p>
                        <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" />
                        <x-text-input id="name"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                            placeholder="John Doe" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <x-input-label for="role_select" value="Tipe Anggota *" />
                        <select id="role_select" name="role"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                            required>
                            <option value="">Pilih Tipe Anggota</option>
                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="umum" {{ old('role') == 'umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <!-- NIM -->
                    <div id="nim-section" style="display: none;">
                        <x-input-label for="nim" value="NIM (untuk Mahasiswa)" />
                        <x-text-input id="nim"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="text" name="nim" :value="old('nim')"
                            placeholder="Contoh: 2021310001" />
                        <x-input-error :messages="$errors->get('nim')" class="mt-1" />
                    </div>

                    <!-- NIK -->
                    <div id="nik-section" style="display: none;">
                        <x-input-label for="nik" value="NIK (Nomor Identitas) *" />
                        <x-text-input id="nik"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="text" name="nik" :value="old('nik')"
                            placeholder="Contoh: 1234567890123456" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input id="email"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="email" name="email" :value="old('email')" required autocomplete="email"
                            placeholder="nama@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Major -->
                    <div id="major-section" style="display: none;">
                        <x-input-label for="major_id" value="Jurusan/Prodi *" />
                        <select id="major_id" name="major_id"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="">Pilih Prodi</option>
                            @foreach ($majors as $major)
                                <option value="{{ $major->id }}"
                                    {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                    {{ $major->code }} - {{ $major->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('major_id')" class="mt-1" />
                    </div>

                    <!-- Angkatan -->
                    <div id="angkatan-section" style="display: none;">
                        <x-input-label for="angkatan" value="Tahun Angkatan *" />
                        <x-text-input id="angkatan"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="number" name="angkatan" :value="old('angkatan', date('Y'))" min="2000"
                            :max="date('Y')" placeholder="{{ date('Y') }}" />
                        <x-input-error :messages="$errors->get('angkatan')" class="mt-1" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" value="No. Telepon/WhatsApp *" />
                        <x-text-input id="phone"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="text" name="phone" :value="old('phone')" required
                            placeholder="081234567890" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Kata Sandi *" />
                        <x-text-input id="password"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="password" name="password" required autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi *" />
                        <x-text-input id="password_confirmation"
                            class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder="Ulangi kata sandi" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required
                            class="mt-1 rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:ring-primary-500">
                        <label for="terms" class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                            Saya setuju dengan
                            <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">syarat
                                dan ketentuan</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:-translate-y-0.5 transition-all">
                            Daftar Sekarang
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Sudah punya akun?
                            <a href="{{ route('login') }}"
                                class="font-bold text-secondary-600 dark:text-secondary-400 hover:text-secondary-700 hover:underline">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>
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

    <script>
        const roleSelect = document.getElementById('role_select');
        const nimSection = document.getElementById('nim-section');
        const nikSection = document.getElementById('nik-section');
        const majorSection = document.getElementById('major-section');
        const angkatanSection = document.getElementById('angkatan-section');

        function toggleFields() {
            const role = roleSelect.value;

            // Reset visibility
            nimSection.style.display = 'none';
            nikSection.style.display = 'none';
            majorSection.style.display = 'none';
            angkatanSection.style.display = 'none';

            // Show/hide based on role
            if (role === 'mahasiswa') {
                nimSection.style.display = 'block';
                majorSection.style.display = 'block';
                angkatanSection.style.display = 'block';
            } else if (role === 'dosen') {
                // Dosen: no nim, no major, no angkatan
            } else if (role === 'umum') {
                nikSection.style.display = 'block';
            }

            // Reset input values when hidden
            document.getElementById('nim').value = '';
            document.getElementById('nik').value = '';
            document.getElementById('major_id').value = '';
            document.getElementById('angkatan').value = new Date().getFullYear();
        }

        roleSelect?.addEventListener('change', toggleFields);
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-guest-layout>
