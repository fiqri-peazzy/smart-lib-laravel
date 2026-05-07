@extends('layouts.app')

@section('title', $book->title . ' - Detail Buku')

@section('content')
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-white dark:bg-gray-900 pt-8 pb-32 border-b border-gray-200 dark:border-gray-800">
        <!-- Subtle Background Glows -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-primary-100 dark:bg-primary-900/10 blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-100 dark:bg-blue-900/10 blur-3xl opacity-50 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors"><i class="bi bi-house-door"></i> Home</a></li>
                    <li><i class="bi bi-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('books.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Books</a></li>
                    <li><i class="bi bi-chevron-right text-xs"></i></li>
                    <li class="font-medium text-gray-900 dark:text-gray-200 truncate">{{ $book->title }}</li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
                <!-- Cover Image (Fixed Max Width) -->
                <div class="w-full sm:w-64 md:w-72 flex-shrink-0 mx-auto md:mx-0">
                    <div class="rounded-2xl shadow-2xl aspect-[2/3] overflow-hidden ring-1 ring-black/5 dark:ring-white/10 group bg-gray-100 dark:bg-gray-800">
                        @if ($book->cover_image)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="bi bi-book text-gray-300 dark:text-gray-600 text-7xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Main Content & Actions -->
                <div class="flex-1 flex flex-col justify-center">
                    <!-- Title & Meta -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($book->categories as $cat)
                                <a href="{{ route('books.category', $cat) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 border border-primary-100 dark:border-primary-800/50 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-3 leading-tight">{{ $book->title }}</h1>
                        @if ($book->subtitle)
                            <p class="text-lg lg:text-xl text-gray-500 dark:text-gray-400 font-medium mb-5">{{ $book->subtitle }}</p>
                        @endif
                        
                        <div class="flex flex-wrap gap-5 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-center"><i class="bi bi-person-fill mr-2 text-primary-500"></i><span class="font-medium text-gray-900 dark:text-gray-100">{{ $book->author }}</span></div>
                            <div class="flex items-center"><i class="bi bi-building mr-2 text-primary-500"></i><span>{{ $book->publisher ?? 'N/A' }}</span></div>
                            <div class="flex items-center"><i class="bi bi-calendar3 mr-2 text-primary-500"></i><span>{{ $book->publication_year ?? 'N/A' }}</span></div>
                        </div>
                    </div>

                    <!-- Action Box (Status + Actions) -->
                    <div class="mt-auto bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row gap-6 items-center">
                            <!-- Status Info -->
                            <div class="w-full sm:w-1/3 flex flex-col items-center sm:items-start text-center sm:text-left border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-gray-700 pb-5 sm:pb-0 sm:pr-6">
                                @if ($book->available_stock > 0)
                                    <div class="flex items-center text-green-600 dark:text-green-400 mb-1.5">
                                        <i class="bi bi-check-circle-fill text-2xl mr-2"></i>
                                        <span class="font-bold text-lg">Tersedia</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $book->available_stock }} dari {{ $book->total_stock }} eksemplar</p>
                                @else
                                    <div class="flex items-center text-red-600 dark:text-red-400 mb-1.5">
                                        <i class="bi bi-x-circle-fill text-2xl mr-2"></i>
                                        <span class="font-bold text-lg">Kosong</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Semua eksemplar dipinjam</p>
                                @endif
                            </div>
                            
                            <!-- Action Button -->
                            <div class="w-full sm:w-2/3">
                                @guest
                                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-6 py-3.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-600/20">
                                        <i class="bi bi-box-arrow-in-right mr-2 text-lg"></i> Login untuk Meminjam
                                    </a>
                                @else
                                    @if ($book->available_stock > 0)
                                        <div class="bg-primary-50 dark:bg-primary-900/20 text-primary-800 dark:text-primary-200 p-4 rounded-xl border border-primary-200 dark:border-primary-800/50 flex items-start">
                                            <i class="bi bi-qr-code-scan text-2xl mr-3 mt-0.5 text-primary-600 dark:text-primary-400"></i>
                                            <p class="text-sm font-medium leading-relaxed">
                                                Silakan datang ke rak <strong class="text-primary-900 dark:text-primary-100">{{ $book->rack_location ?? 'Perpustakaan' }}</strong> dan gunakan menu <strong>Scan & Pinjam</strong> di HP Anda.
                                            </p>
                                        </div>
                                    @elseif ($book->available_stock <= 0)
                                        @if ($hasBooking)
                                            <button type="button" disabled class="w-full flex items-center justify-center px-6 py-3.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl font-bold cursor-not-allowed border border-gray-200 dark:border-gray-600">
                                                <i class="bi bi-check-circle-fill mr-2 text-lg"></i> Sudah Masuk Antrean
                                            </button>
                                        @else
                                            <button type="button" onclick="openBookingModal()" class="w-full flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-bold hover:from-yellow-600 hover:to-orange-600 transition-all shadow-lg shadow-yellow-500/25">
                                                <i class="bi bi-bell-fill mr-2 text-lg"></i> Beritahu Jika Tersedia
                                            </button>
                                        @endif
                                    @endif

                                    @if ($borrowMessage && $book->available_stock > 0)
                                        <div class="mt-3 p-2.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400 text-xs rounded-lg flex items-start border border-yellow-200 dark:border-yellow-800/50">
                                            <i class="bi bi-info-circle-fill mr-2 mt-0.5"></i> <span class="font-medium">{{ $borrowMessage }}</span>
                                        </div>
                                    @endif
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Content Section -->
    <div class="bg-gray-50 dark:bg-gray-950 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Quick Stats Cards (Overlapping the Hero) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8 lg:mb-12 -mt-16 relative z-20">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 sm:p-6 text-center transform transition-transform hover:-translate-y-1">
                    <i class="bi bi-collection text-3xl text-primary-500 mb-3 opacity-80"></i>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-1">{{ $book->total_stock }}</div>
                    <div class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Total Buku</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 sm:p-6 text-center transform transition-transform hover:-translate-y-1">
                    <i class="bi bi-journal-bookmark text-3xl text-primary-500 mb-3 opacity-80"></i>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-1">{{ $book->edition ?? '1' }}</div>
                    <div class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Edisi</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 sm:p-6 text-center transform transition-transform hover:-translate-y-1">
                    <i class="bi bi-file-earmark-text text-3xl text-primary-500 mb-3 opacity-80"></i>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-1">{{ $book->pages ?? 'N/A' }}</div>
                    <div class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Halaman</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 sm:p-6 text-center transform transition-transform hover:-translate-y-1">
                    <i class="bi bi-translate text-3xl text-primary-500 mb-3 opacity-80"></i>
                    <div class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-1">{{ $book->language ?? 'ID' }}</div>
                    <div class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Bahasa</div>
                </div>
            </div>

            <!-- Detail & Description Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Sidebar: Detail Buku -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-info-circle mr-3 text-primary-500 text-xl"></i> Informasi Detail
                        </h3>
                        <ul class="space-y-5">
                            <li class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">ISBN</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $book->isbn ?? 'N/A' }}</span>
                            </li>
                            <li class="flex flex-col border-t border-gray-100 dark:border-gray-700 pt-5">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Lokasi Rak</span>
                                <span class="font-medium text-gray-900 dark:text-white flex items-center">
                                    <i class="bi bi-geo-alt-fill text-primary-500 mr-2"></i> {{ $book->rack_location ?? 'N/A' }}
                                </span>
                            </li>
                            @if ($book->recommendedForMajor)
                            <li class="flex flex-col border-t border-gray-100 dark:border-gray-700 pt-5">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-2">Rekomendasi Jurusan</span>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 w-fit">
                                    <i class="bi bi-star-fill mr-1.5 text-yellow-500"></i> {{ $book->recommendedForMajor->name }}
                                </span>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Right Main Content: Description & Related -->
                <div class="lg:col-span-2 space-y-8">
                    @if ($book->description)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <i class="bi bi-text-paragraph mr-3 text-primary-500 text-xl"></i> Deskripsi Buku
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed text-[15px]">
                                {!! nl2br(e($book->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Related Books -->
                    @if ($relatedBooks->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <i class="bi bi-collection mr-3 text-primary-500 text-xl"></i> Buku Terkait
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 lg:gap-6">
                                @foreach ($relatedBooks as $relatedBook)
                                    <a href="{{ route('books.show', $relatedBook) }}"
                                        class="group bg-gray-50 dark:bg-gray-700/30 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                                        <div class="relative aspect-[2/3] bg-primary-50 dark:bg-gray-800 flex-shrink-0">
                                            @if ($relatedBook->cover_image)
                                                <img src="{{ $relatedBook->cover_url }}" alt="{{ $relatedBook->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-primary-300 dark:text-gray-600">
                                                    <i class="bi bi-book text-4xl opacity-50"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Badge -->
                                            <div class="absolute top-2 right-2">
                                                @if ($relatedBook->available_stock > 0)
                                                    <span class="inline-flex items-center px-2 py-1 bg-green-500/95 backdrop-blur-sm text-white text-[10px] font-bold rounded-md shadow-sm">
                                                        Tersedia
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 bg-red-500/95 backdrop-blur-sm text-white text-[10px] font-bold rounded-md shadow-sm">
                                                        Kosong
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-4 flex-1 flex flex-col">
                                            <h3 class="font-bold text-sm text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 mb-1.5 leading-snug">
                                                {{ $relatedBook->title }}
                                            </h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-auto">
                                                {{ $relatedBook->author }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
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
                        <i class="bi bi-bell-fill mr-3"></i>
                        Pemberitahuan Ketersediaan
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
                            <span class="font-semibold">Seluruh eksemplar sedang dipinjam</span>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="bi bi-info-circle-fill text-yellow-600 dark:text-yellow-400 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <h5 class="text-yellow-900 dark:text-yellow-300 font-bold mb-2">Sistem Antrean Perpustakaan (Otomatis):
                            </h5>
                            <ul class="text-sm text-yellow-800 dark:text-yellow-300 space-y-1 list-disc list-inside">
                                <li>Anda akan otomatis masuk ke <strong>daftar tunggu</strong> peminjaman.</li>
                                <li>Admin <strong>tidak perlu konfirmasi</strong>. Sistem akan langsung memberi notifikasi saat buku dikembalikan oleh peminjam lain.</li>
                                <li>Setelah dinotifikasi, statusnya akan menjadi <strong>Siap Diambil</strong> dan Anda punya waktu <strong>24 jam</strong> untuk mengambil buku secara fisik di perpustakaan.</li>
                                <li>Prioritas akan diberikan berdasarkan urutan waktu Anda masuk ke antrean.</li>
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
                        <i class="bi bi-check-circle mr-2"></i>
                        Masuk Daftar Tunggu
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    </script>
@endsection
