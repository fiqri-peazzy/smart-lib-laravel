@extends('layouts.app')

@section('title', $book->title . ' - Detail Buku')

@section('content')
    <!-- Hero Section -->
    <section class="bg-primary-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Book Cover -->
                <div class="lg:w-1/4">
                    <div
                        class="bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        @if ($book->cover_image)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-auto">
                        @else
                            <div
                                class="w-full aspect-[2/3] bg-primary-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="bi bi-book text-primary-300 dark:text-gray-500 text-8xl opacity-50"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Book Info -->
                <div class="lg:w-3/4">
                    <!-- Breadcrumb -->
                    <nav class="mb-4">
                        <ol class="flex items-center space-x-2 text-sm text-primary-200">
                            <li><a href="{{ route('home') }}" class="hover:text-white"><i class="bi bi-house-door"></i>
                                    Home</a></li>
                            <li><i class="bi bi-chevron-right text-xs"></i></li>
                            <li><a href="{{ route('books.index') }}" class="hover:text-white">Books</a></li>
                            <li><i class="bi bi-chevron-right text-xs"></i></li>
                            <li class="text-white font-medium truncate">{{ $book->title }}</li>
                        </ol>
                    </nav>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-3">{{ $book->title }}</h1>
                    @if ($book->subtitle)
                        <p class="text-xl text-primary-100 mb-4 font-medium">{{ $book->subtitle }}</p>
                    @endif

                    <!-- Author & Publisher -->
                    <div class="flex flex-wrap gap-4 mb-6 text-primary-100">
                        <div class="flex items-center">
                            <i class="bi bi-person-fill mr-2"></i>
                            <span>{{ $book->author }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-building mr-2"></i>
                            <span>{{ $book->publisher ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="bi bi-calendar-event mr-2"></i>
                            <span>{{ $book->publication_year ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach ($book->categories as $cat)
                            <a href="{{ route('books.category', $cat) }}"
                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-white/20 hover:bg-white/30 transition-colors">
                                <i class="bi bi-tag-fill mr-1"></i>
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->available_stock }}</div>
                            <div class="text-sm text-primary-100">Tersedia</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->total_stock }}</div>
                            <div class="text-sm text-primary-100">Total Eksemplar</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->pages ?? 'N/A' }}</div>
                            <div class="text-sm text-primary-100">Halaman</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->language ?? 'ID' }}</div>
                            <div class="text-sm text-primary-100">Bahasa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <aside class="lg:w-1/4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-24 space-y-6">
                        <!-- Book Details -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="bi bi-info-circle mr-2"></i>
                                Detail Buku
                            </h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <dt class="text-gray-600 dark:text-gray-400">ISBN</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $book->isbn ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <dt class="text-gray-600 dark:text-gray-400">Edisi</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $book->edition ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <dt class="text-gray-600 dark:text-gray-400">Rak</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">
                                        {{ $book->rack_location ?? 'N/A' }}</dd>
                                </div>
                                @if ($book->recommendedForMajor)
                                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                        <dt class="text-gray-600 dark:text-gray-400">Rekomendasi</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white text-right">
                                            {{ $book->recommendedForMajor->name }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Availability Status -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="bi bi-clipboard-check mr-2"></i>
                                Status Ketersediaan
                            </h3>
                            @if ($book->available_stock > 0)
                                <div class="p-4 bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-xl">
                                    <div class="flex items-center justify-center text-green-700 dark:text-green-400 mb-2">
                                        <i class="bi bi-check-circle-fill text-2xl mr-2"></i>
                                        <span class="font-bold text-lg">Tersedia</span>
                                    </div>
                                    <p class="text-sm text-center text-green-600 dark:text-green-400">
                                        {{ $book->available_stock }} dari {{ $book->total_stock }} eksemplar siap dipinjam
                                    </p>
                                </div>
                            @else
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-xl">
                                    <div class="flex items-center justify-center text-red-700 dark:text-red-400 mb-2">
                                        <i class="bi bi-x-circle-fill text-2xl mr-2"></i>
                                        <span class="font-bold text-lg">Tidak Tersedia</span>
                                    </div>
                                    <p class="text-sm text-center text-red-600 dark:text-red-400">
                                        Semua eksemplar sedang dipinjam
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
                            @guest
                                <!-- Not Logged In -->
                                <a href="{{ route('login') }}"
                                    class="w-full flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-colors">
                                    <i class="bi bi-box-arrow-in-right mr-2"></i>
                                    Login untuk Meminjam
                                </a>
                            @else
                                @if ($canBorrow)
                                    <!-- Can Borrow -->
                                    <button type="button" onclick="openRequestModal()"
                                        class="w-full flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors">
                                        <i class="bi bi-bookmark-check mr-2"></i>
                                        Request Loan
                                    </button>
                                @elseif ($book->available_stock <= 0)
                                    <!-- Book Now (if not already booked) -->
                                    @if ($hasBooking)
                                        <button type="button" disabled
                                            class="w-full flex items-center justify-center px-6 py-3 bg-gray-400 text-white rounded-xl font-semibold cursor-not-allowed">
                                            <i class="bi bi-check-circle mr-2"></i>
                                            Sudah Dibooking
                                        </button>
                                    @else
                                        <button type="button" onclick="openBookingModal()"
                                            class="w-full flex items-center justify-center px-6 py-3 bg-yellow-600 text-white rounded-xl font-semibold hover:bg-yellow-700 transition-colors shadow-lg shadow-yellow-500/20">
                                            <i class="bi bi-calendar-plus mr-2"></i>
                                            Book Now
                                        </button>
                                    @endif
                                @else
                                    <!-- Cannot Borrow -->
                                    <button type="button" disabled
                                        class="w-full flex items-center justify-center px-6 py-3 bg-gray-400 text-white rounded-xl font-semibold cursor-not-allowed">
                                        <i class="bi bi-x-circle mr-2"></i>
                                        Tidak Dapat Meminjam
                                    </button>
                                @endif

                                <!-- Borrow Message -->
                                @if ($borrowMessage)
                                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-lg">
                                        <p class="text-xs text-yellow-800 dark:text-yellow-400 flex items-start">
                                            <i class="bi bi-info-circle-fill mr-2 mt-0.5 flex-shrink-0"></i>
                                            <span>{{ $borrowMessage }}</span>
                                        </p>
                                    </div>
                                @endif
                            @endguest

                            <!-- Back to Catalog -->
                            <a href="{{ route('books.index') }}"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <i class="bi bi-arrow-left mr-2"></i>
                                Kembali ke Katalog
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="lg:w-3/4 space-y-8">
                    <!-- Description -->
                    @if ($book->description)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="bi bi-file-text mr-2"></i>
                                Deskripsi
                            </h2>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $book->description }}</p>
                            </div>
                        </div>
                    @endif



                    <!-- Related Books -->
                    @if ($relatedBooks->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <i class="bi bi-collection mr-2"></i>
                                Buku Terkait
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach ($relatedBooks as $relatedBook)
                                    <a href="{{ route('books.show', $relatedBook) }}"
                                        class="group bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                        <div class="relative aspect-w-16 aspect-h-9 bg-primary-100 dark:bg-gray-700">
                                            @if ($relatedBook->cover_image)
                                                <img src="{{ $relatedBook->cover_url }}" alt="{{ $relatedBook->title }}"
                                                    class="w-full h-32 object-cover">
                                            @else
                                                <div class="w-full h-32 flex items-center justify-center text-white">
                                                    <i class="bi bi-book text-3xl opacity-50"></i>
                                                </div>
                                            @endif
                                            <div class="absolute top-2 right-2">
                                                @if ($relatedBook->available_stock > 0)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                                        <i class="bi bi-check-circle mr-1"></i>
                                                        {{ $relatedBook->available_stock }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                                        <i class="bi bi-x-circle"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <h3
                                                class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 mb-1">
                                                {{ $relatedBook->title }}
                                            </h3>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-1">
                                                <i class="bi bi-person mr-1"></i>
                                                {{ $relatedBook->author }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>

    <!-- Request Loan Modal -->
    <div id="requestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full flex flex-col max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Request Peminjaman
                        </h3>
                        <p class="text-blue-100 text-sm mt-0.5">Konfirmasi pengajuan pinjam buku</p>
                    </div>
                    <button onclick="closeRequestModal()" class="text-white/70 hover:text-white transition-colors p-1">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4 overflow-y-auto custom-scrollbar">
                <!-- Book Info -->
                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                        class="w-16 h-22 object-cover rounded-lg shadow flex-shrink-0"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2288%22 viewBox=%220 0 64 88%22%3E%3Crect width=%2264%22 height=%2288%22 fill=%22%23e5e7eb%22 rx=%224%22/%3E%3Ctext x=%2232%22 y=%2248%22 font-size=%2224%22 text-anchor=%22middle%22 fill=%22%239ca3af%22%3E📖%3C/text%3E%3C/svg%3E'">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 dark:text-white leading-tight line-clamp-2">{{ $book->title }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $book->author }}</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                            <span class="text-sm text-green-600 dark:text-green-400 font-semibold">{{ $book->available_stock }} eksemplar tersedia</span>
                        </div>
                    </div>
                </div>

                <!-- Steps / Alur Baru -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                    <h5 class="text-blue-800 dark:text-blue-300 font-bold text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-list-ol"></i>
                        Alur Peminjaman
                    </h5>
                    <ol class="space-y-2.5">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Klik "Konfirmasi Request"</strong> — sistem akan mencatat pengajuan pinjam Anda
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Datang ke perpustakaan</strong> dalam <strong>3 hari</strong> — bawa kartu mahasiswa/identitas
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Staff scan QR Code</strong> yang ada di sampul buku untuk mengaktifkan peminjaman
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-green-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">✓</span>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                Masa pinjam <strong>14 hari</strong> dihitung mulai saat buku diambil
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Warning: auto-cancel -->
                <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg flex-shrink-0"></i>
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        Request akan <strong>otomatis dibatalkan</strong> jika buku tidak diambil dalam 3 hari.
                    </p>
                </div>

                <!-- Your Stats -->
                @auth
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Credit Score</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ Auth::user()->credit_score }}
                                <span class="text-xs font-normal text-gray-400">/100</span>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Peminjaman Aktif</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ Auth::user()->activeLoans()->count() }}
                                <span class="text-xs font-normal text-gray-400">/ {{ Auth::user()->max_loans }} maks</span>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Modal Footer -->
            <div class="px-5 pb-5 flex gap-3">
                <button onclick="closeRequestModal()"
                    class="flex-1 px-5 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Batal
                </button>
                <form action="{{ route('books.request-loan', $book) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        Konfirmasi Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full flex flex-col max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold flex items-center">
                        <i class="bi bi-calendar-check mr-3"></i>
                        Pesan Buku (Booking)
                    </h3>
                    <button onclick="closeBookingModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar">
                <!-- Book Info -->
                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                        class="w-20 h-28 object-cover rounded-lg shadow">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-1 line-clamp-2">{{ $book->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $book->author }}</p>
                        <div class="flex items-center text-sm text-red-600 dark:text-red-400">
                            <i class="bi bi-info-circle-fill mr-1 text-yellow-500"></i>
                            <span class="font-semibold">Stok fisik sedang kosong</span>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="bi bi-info-circle-fill text-yellow-600 dark:text-yellow-400 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <h5 class="text-yellow-900 dark:text-yellow-300 font-bold mb-2">Sistem Antrian Perpustakaan:
                            </h5>
                            <ul class="text-sm text-yellow-800 dark:text-yellow-300 space-y-1 list-disc list-inside">
                                <li>Anda akan masuk ke dalam <strong>daftar tunggu</strong> peminjaman</li>
                                <li>Kami akan memberikan notifikasi saat buku sudah tersedia kembali</li>
                                <li>Setelah tersedia, Anda punya <strong>3 hari</strong> untuk mengambil buku</li>
                                <li>Prioritas diberikan berdasarkan urutan waktu booking</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 bg-gray-50 dark:bg-gray-700/50 flex gap-3">
                <button onclick="closeBookingModal()"
                    class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Batal
                </button>
                <form action="{{ route('bookings.create') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <button type="submit"
                        class="w-full px-6 py-3 bg-yellow-600 text-white rounded-xl font-semibold hover:bg-yellow-700 transition-colors shadow-lg">
                        <i class="bi bi-calendar-plus mr-2"></i>
                        Konfirmasi Booking
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        function openRequestModal() {
            const modal = document.getElementById('requestModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeRequestModal() {
            const modal = document.getElementById('requestModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function openBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRequestModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('requestModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestModal();
            }
        });

        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    </script>
@endsection
