@extends('layouts.app')

@section('title', 'Home - Smart Digital Library')

@section('content')

    <!-- Hero Section -->
    <section
        class="relative bg-white dark:bg-gray-900 text-gray-900 dark:text-white overflow-hidden pt-12 pb-20 md:pt-20 md:pb-28">
        <!-- Abstract Background Decoration representing Kampus Identity -->
        <div class="absolute inset-0 z-0 opacity-100 pointer-events-none">
            <!-- Top Left Pattern/Wave -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary-100 dark:bg-primary-900/30 rounded-br-full opacity-60">
            </div>
            <!-- Bottom Right Shape -->
            <div
                class="absolute bottom-0 right-0 w-96 h-96 bg-secondary-100 dark:bg-secondary-900/30 rounded-tl-full opacity-60">
            </div>
            <!-- Dots -->
            <div class="absolute top-20 right-20 w-32 h-32 opacity-20"
                style="background-image: radial-gradient(#1e3a8a 2px, transparent 2px); background-size: 16px 16px;"></div>
            <div class="absolute bottom-10 left-10 w-40 h-32 opacity-20"
                style="background-image: radial-gradient(#c89b4f 2px, transparent 2px); background-size: 16px 16px;"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <div space-y-6 data-aos="fade-up">
                <!-- Main Typography -->
                <h1
                    class="text-4xl md:text-6xl font-black tracking-tight text-primary-900 dark:text-white uppercase leading-none drop-shadow-sm">
                    Fakultas Ilmu Komputer
                    <br />
                    <span class="text-3xl md:text-secondary-900 dark:text-secondary-400 mt-2 block tracking-wide">
                        Universitas Ichsan Gorontalo
                    </span>
                </h1>

                <p
                    class="mt-6 text-lg md:text-xl text-primary-800 dark:text-primary-200 font-medium max-w-3xl mx-auto leading-relaxed">
                    Menjadi Fakultas Berdaya Saing di Bidang Ilmu Komputer dan Desain Berbasis <span
                        class="italic font-bold">Technopreneur</span> pada Tahun 2044
                </p>

                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('books.index') }}"
                        class="px-8 py-4 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 hover:shadow-xl hover:shadow-primary-500/30 transform hover:-translate-y-1 transition-all">
                        <i class="bi bi-book me-2"></i> Telusuri Buku
                    </a>
                    <a href="{{ route('digital.index') }}"
                        class="px-8 py-4 bg-secondary-500 text-white rounded-xl font-bold hover:bg-secondary-600 hover:shadow-xl hover:shadow-secondary-500/30 transform hover:-translate-y-1 transition-all">
                        <i class="bi bi-laptop me-2"></i> Perpustakaan Digital
                    </a>
                </div>

                <!-- Stats -->
                <div class="mt-12 grid grid-cols-2 gap-4 max-w-xl mx-auto">
                    <div
                        class="flex flex-col items-center justify-center bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-3xl font-black text-primary-900 dark:text-white">{{ $stats['total_books'] }}+</div>
                        <div class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Total
                            Buku</div>
                    </div>
                    <div
                        class="flex flex-col items-center justify-center bg-white/80 dark:bg-gray-800/80 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-3xl font-black text-secondary-600 dark:text-secondary-400">
                            {{ $stats['available_books'] }}+</div>
                        <div class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">
                            Tersedia</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-white dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Jelajahi Kategori
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Temukan buku dari berbagai kategori teknologi informasi
                </p>
            </div>

            @php
                $icons = ['bi-book', 'bi-database', 'bi-globe', 'bi-phone', 'bi-robot', 'bi-shield-lock'];
                // Group categories into chunks of 6 for each slide
                $categoryChunks = $popularCategories->chunk(6);
            @endphp

            <!-- Categories Slider with Alpine.js -->
            <div x-data="{
                currentSlide: 0,
                totalSlides: {{ $categoryChunks->count() }},
                autoplayInterval: null,
                init() {
                    this.startAutoplay();
                },
                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.next();
                    }, 3000);
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                        this.autoplayInterval = null;
                    }
                },
                next() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                },
                prev() {
                    this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                },
                goTo(index) {
                    this.currentSlide = index;
                }
            }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" class="relative">

                <!-- Slider Container -->
                <div class="overflow-hidden">
                    <div class="relative">
                        @foreach ($categoryChunks as $chunkIndex => $chunk)
                            <div x-show="currentSlide === {{ $chunkIndex }}"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-x-8"
                                x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform -translate-x-8"
                                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 px-4">
                                @foreach ($chunk as $category)
                                    <a href="{{ route('books.index', ['category' => $category->id]) }}"
                                        class="group p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                                        <div class="w-12 h-12 mx-auto mb-4 rounded-lg flex items-center justify-center text-2xl"
                                            style="background-color: {{ $category->color }}20;">
                                            <i class="bi {{ $icons[($chunkIndex * 6 + $loop->index) % 6] }}"
                                                style="color: {{ $category->color }};"></i>
                                        </div>
                                        <h3
                                            class="text-sm font-bold text-center text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                            {{ $category->name }}
                                        </h3>
                                        <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $category->books_count }} buku
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation Arrows -->
                @if ($categoryChunks->count() > 1)
                    <button @click="prev()" type="button"
                        class="absolute top-1/2 left-0 z-10 -translate-y-1/2 bg-white/90 dark:bg-gray-800/90 hover:bg-white dark:hover:bg-gray-700 rounded-full p-3 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="bi bi-chevron-left text-gray-800 dark:text-white text-xl"></i>
                    </button>

                    <button @click="next()" type="button"
                        class="absolute top-1/2 right-0 z-10 -translate-y-1/2 bg-white/90 dark:bg-gray-800/90 hover:bg-white dark:hover:bg-gray-700 rounded-full p-3 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="bi bi-chevron-right text-gray-800 dark:text-white text-xl"></i>
                    </button>
                @endif

                <!-- Indicators -->
                @if ($categoryChunks->count() > 1)
                    <div class="flex justify-center gap-2 mt-6">
                        @foreach ($categoryChunks as $chunkIndex => $chunk)
                            <button @click="goTo({{ $chunkIndex }})" type="button"
                                class="h-2 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :class="currentSlide === {{ $chunkIndex }} ? 'bg-indigo-600 w-8' :
                                    'bg-gray-300 hover:bg-gray-400 w-2'">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Featured Books -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800/50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Buku Unggulan
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Koleksi terbaik pilihan perpustakaan
                    </p>
                </div>
                <a href="{{ route('books.index') }}"
                    class="text-primary-600 dark:text-primary-400 hover:underline font-bold" data-aos="fade-left">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($featuredBooks as $book)
                    <a href="{{ route('books.show', $book) }}"
                        class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="aspect-w-16 aspect-h-9 bg-primary-100 dark:bg-gray-700 relative overflow-hidden">
                            @if ($book->cover_image)
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                                    class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-48 flex items-center justify-center text-white text-6xl">
                                    <i class="bi bi-book"></i>
                                </div>
                            @endif
                            <!-- Badge -->
                            <div class="absolute top-4 right-4">
                                @if ($book->available_stock > 0)
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                        Dipinjam
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach ($book->categories->take(2) as $cat)
                                    <span class="px-2 py-1 text-xs rounded-full"
                                        style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                        {{ $cat->name }}
                                    </span>
                                @endforeach
                            </div>
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ $book->title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ $book->author }}
                            </p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $book->rack_location }}
                                </span>
                                <span class="font-bold text-primary-600 dark:text-primary-400">
                                    {{ $book->available_stock }}/{{ $book->total_stock }} available
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest Digital -->
    <section class="py-16 bg-white dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Koleksi Digital Terbaru
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        E-books dan jurnal terkini
                    </p>
                </div>
                <a href="{{ route('digital.index') }}"
                    class="text-primary-600 dark:text-primary-400 hover:underline font-bold" data-aos="fade-left">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($latestDigital as $digital)
                    <a href="{{ route('digital.show', $digital) }}"
                        class="group bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 hover:shadow-xl transform hover:-translate-y-1 transition-all"
                        data-aos="flip-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div
                            class="w-16 h-16 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">
                            @php
                                $digitalIcons = [
                                    'bi-file-earmark-text',
                                    'bi-bar-chart',
                                    'bi-mortarboard',
                                    'bi-pencil-square',
                                ];
                            @endphp
                            <i
                                class="bi {{ $digitalIcons[$loop->index % 4] }} text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            {{ $digital->title }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            {{ $digital->author }}
                        </p>
                        <div class="flex items-center justify-between text-xs">
                            <span
                                class="px-2 py-1 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-md font-bold">
                                {{ strtoupper($digital->type) }}
                            </span>
                            <span class="text-gray-500">
                                {{ $digital->year }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary-600 text-white relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl rounded-none transform translate-x-1/2 -translate-y-1/2">
            </div>
        </div>
        <div class="max-w-4xl mx-auto text-center px-4 relative z-10" data-aos="zoom-in">
            <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-6">
                Siap Memulai Perjalanan Literasi?
            </h2>
            <p class="text-xl text-primary-100 mb-8 font-medium">
                Bergabunglah dengan ribuan mahasiswa yang sudah merasakan kemudahan akses perpustakaan digital
            </p>
            @guest
                <a href=""
                    class="inline-block px-8 py-4 bg-gray-900 text-white rounded-xl font-bold hover:shadow-2xl transform hover:-translate-y-1 transition-all">
                    Daftar Sekarang - Gratis!
                </a>
            @else
                <a href="{{ route('books.index') }}"
                    class="inline-block px-8 py-4 bg-gray-900 text-white rounded-xl font-bold hover:shadow-2xl transform hover:-translate-y-1 transition-all border border-gray-700 hover:border-gray-600">
                    Mulai Jelajahi Koleksi
                </a>
            @endguest
        </div>
    </section>

@endsection

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(3deg);
            }

            50% {
                transform: translateY(-20px) rotate(3deg);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
@endpush
