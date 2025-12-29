@extends('layouts.app')

@section('title', $book->title . ' - Detail Buku')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12">
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
                                class="w-full aspect-[2/3] bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <i class="bi bi-book text-white text-8xl opacity-50"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Book Info -->
                <div class="lg:w-3/4">
                    <!-- Breadcrumb -->
                    <nav class="mb-4">
                        <ol class="flex items-center space-x-2 text-sm text-indigo-200">
                            <li><a href="{{ route('home') }}" class="hover:text-white"><i class="bi bi-house-door"></i>
                                    Home</a></li>
                            <li><i class="bi bi-chevron-right text-xs"></i></li>
                            <li><a href="{{ route('books.index') }}" class="hover:text-white">Books</a></li>
                            <li><i class="bi bi-chevron-right text-xs"></i></li>
                            <li class="text-white font-medium truncate">{{ $book->title }}</li>
                        </ol>
                    </nav>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $book->title }}</h1>
                    @if ($book->subtitle)
                        <p class="text-xl text-indigo-100 mb-4">{{ $book->subtitle }}</p>
                    @endif

                    <!-- Author & Publisher -->
                    <div class="flex flex-wrap gap-4 mb-6 text-indigo-100">
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
                            <div class="text-sm text-indigo-100">Tersedia</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->total_stock }}</div>
                            <div class="text-sm text-indigo-100">Total Eksemplar</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->pages ?? 'N/A' }}</div>
                            <div class="text-sm text-indigo-100">Halaman</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <div class="text-3xl font-bold mb-1">{{ $book->language ?? 'ID' }}</div>
                            <div class="text-sm text-indigo-100">Bahasa</div>
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
                                    class="w-full flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                                    <i class="bi bi-box-arrow-in-right mr-2"></i>
                                    Login untuk Meminjam
                                </a>
                            @else
                                @if ($canBorrow)
                                    <!-- Can Borrow -->
                                    <button type="button" onclick="alert('Fitur Request Loan akan segera diimplementasi')"
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
                                        <button type="button" onclick="alert('Fitur Booking akan segera diimplementasi')"
                                            class="w-full flex items-center justify-center px-6 py-3 bg-yellow-600 text-white rounded-xl font-semibold hover:bg-yellow-700 transition-colors">
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

                    <!-- Book Items (Copies) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-stack mr-2"></i>
                            Daftar Eksemplar
                            <span class="ml-3 text-sm font-normal text-gray-500">{{ $book->items->count() }}
                                eksemplar</span>
                        </h2>

                        @if ($book->items->isEmpty())
                            <div class="text-center py-8">
                                <i class="bi bi-inbox text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Belum ada eksemplar fisik untuk buku ini</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                                                Barcode</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                                                Kondisi</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                                                Status</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">
                                                Lokasi</th>
                                            <th
                                                class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                                                QR Code</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($book->items as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                                    {{ $item->barcode }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if ($item->condition === 'excellent') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                        @elseif($item->condition === 'good') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                                        @elseif($item->condition === 'fair') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                                        <i class="bi bi-star-fill mr-1"></i>
                                                        {{ ucfirst($item->condition) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if ($item->status === 'available') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                        @elseif($item->status === 'on_loan') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                                        @if ($item->status === 'available')
                                                            <i class="bi bi-check-circle-fill mr-1"></i>
                                                            Available
                                                        @elseif($item->status === 'on_loan')
                                                            <i class="bi bi-hourglass-split mr-1"></i>
                                                            On Loan
                                                        @else
                                                            <i class="bi bi-x-circle-fill mr-1"></i>
                                                            {{ ucfirst($item->status) }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                                    <i class="bi bi-geo-alt text-gray-400 mr-1"></i>
                                                    {{ $item->current_location ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($item->qr_code_url)
                                                        <a href="{{ $item->qr_code_url }}" target="_blank"
                                                            class="inline-block p-1 bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                            <img src="{{ $item->qr_code_url }}"
                                                                alt="QR Code {{ $item->barcode }}"
                                                                class="w-12 h-12 object-contain">
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 text-xs">No QR</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

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
                                        <div
                                            class="relative aspect-w-16 aspect-h-9 bg-gradient-to-br from-indigo-500 to-purple-600">
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
                                                class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 mb-1">
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
@endsection
