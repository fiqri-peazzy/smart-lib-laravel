@extends('layouts.app')

@section('title', 'Home - Smart Digital Library')

@section('content')

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20">
            <div
                class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full mix-blend-multiply filter blur-xl animate-blob">
            </div>
            <div
                class="absolute top-40 right-20 w-72 h-72 bg-yellow-200 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-20 left-40 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-8" data-aos="fade-right">
                    <h1 class="text-4xl md:text-6xl font-bold leading-tight">
                        Perpustakaan Digital
                        <span class="block bg-gradient-to-r from-yellow-200 to-pink-200 bg-clip-text text-transparent">
                            Masa Depan
                        </span>
                    </h1>
                    <p class="text-xl text-indigo-100">
                        Akses ribuan buku dan jurnal dari perpustakaan Fakultas Ilmu Komputer. Pinjam, baca, dan kembangkan
                        ilmu Anda kapan saja, di mana saja.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('books.index') }}"
                            class="px-8 py-4 bg-white text-indigo-600 rounded-xl font-semibold hover:shadow-2xl hover:shadow-white/50 transform hover:-translate-y-1 transition-all">
                            <i class="bi bi-book me-2"></i> Browse Books
                        </a>
                        <a href="{{ route('digital.index') }}"
                            class="px-8 py-4 bg-indigo-500/30 backdrop-blur-lg border border-white/20 rounded-xl font-semibold hover:bg-indigo-500/50 transition-all">
                            <i class="bi bi-laptop me-2"></i> Digital Library
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $stats['total_books'] }}+</div>
                            <div class="text-sm text-indigo-200">Total Buku</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $stats['total_digital'] }}+</div>
                            <div class="text-sm text-indigo-200">E-Books</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $stats['available_books'] }}+</div>
                            <div class="text-sm text-indigo-200">Tersedia</div>
                        </div>
                    </div>
                </div>

                <div class="relative" data-aos="fade-left">
                    <div class="relative z-10">
                        {{-- <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800" alt="Library"
                            class="rounded-2xl shadow-2xl"> --}}
                    </div>
                    <!-- Floating Cards -->
                    <div
                        class="absolute top-10 -right-5 bg-white text-gray-900 rounded-xl p-4 shadow-xl transform rotate-3 animate-float">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-book-half text-indigo-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold">Clean Code</div>
                                <div class="text-sm text-gray-500">Available</div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="absolute bottom-10 -left-5 bg-white text-gray-900 rounded-xl p-4 shadow-xl transform -rotate-3 animate-float animation-delay-2000">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-database text-purple-600 text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold">Data Science</div>
                                <div class="text-sm text-gray-500">4 copies</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Jelajahi Kategori
                </h2>
                <p class="text-gray-600">
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
                                        class="group p-6 bg-gray-50 rounded-2xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                                        <div class="w-12 h-12 mx-auto mb-4 rounded-lg flex items-center justify-center text-2xl"
                                            style="background-color: {{ $category->color }}20;">
                                            <i class="bi {{ $icons[($chunkIndex * 6 + $loop->index) % 6] }}"
                                                style="color: {{ $category->color }};"></i>
                                        </div>
                                        <h3
                                            class="text-sm font-semibold text-center text-gray-900 group-hover:text-indigo-600 transition-colors">
                                            {{ $category->name }}
                                        </h3>
                                        <p class="text-xs text-center text-gray-500 mt-1">
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
                        class="absolute top-1/2 left-0 z-10 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-3 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="bi bi-chevron-left text-gray-800 text-xl"></i>
                    </button>

                    <button @click="next()" type="button"
                        class="absolute top-1/2 right-0 z-10 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-3 shadow-lg transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <i class="bi bi-chevron-right text-gray-800 text-xl"></i>
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
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        Buku Unggulan
                    </h2>
                    <p class="text-gray-600">
                        Koleksi terbaik pilihan perpustakaan
                    </p>
                </div>
                <a href="{{ route('books.index') }}" class="text-indigo-600 hover:underline font-semibold"
                    data-aos="fade-left">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($featuredBooks as $book)
                    <a href="{{ route('books.show', $book) }}"
                        class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div
                            class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden">
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
                                class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ $book->title }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                {{ $book->author }}
                            </p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $book->rack_location }}
                                </span>
                                <span class="font-semibold text-indigo-600">
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
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div data-aos="fade-right">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                        Koleksi Digital Terbaru
                    </h2>
                    <p class="text-gray-600">
                        E-books dan jurnal terkini
                    </p>
                </div>
                <a href="{{ route('digital.index') }}" class="text-indigo-600 hover:underline font-semibold"
                    data-aos="fade-left">
                    Lihat Semua →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($latestDigital as $digital)
                    <a href="{{ route('digital.show', $digital) }}"
                        class="group bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 hover:shadow-xl transform hover:-translate-y-2 transition-all"
                        data-aos="flip-left" data-aos-delay="{{ $loop->index * 100 }}">
                        <div
                            class="w-16 h-16 bg-white rounded-xl flex items-center justify-center text-3xl mb-4 group-hover:scale-110 transition-transform">
                            @php
                                $digitalIcons = [
                                    'bi-file-earmark-text',
                                    'bi-bar-chart',
                                    'bi-mortarboard',
                                    'bi-pencil-square',
                                ];
                            @endphp
                            <i class="bi {{ $digitalIcons[$loop->index % 4] }} text-indigo-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $digital->title }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ $digital->author }}
                        </p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-600 rounded-full font-semibold">
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
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4" data-aos="zoom-in">
            <h2 class="text-3xl md:text-5xl font-bold mb-6">
                Siap Memulai Perjalanan Literasi?
            </h2>
            <p class="text-xl text-indigo-100 mb-8">
                Bergabunglah dengan ribuan mahasiswa yang sudah merasakan kemudahan akses perpustakaan digital
            </p>
            @guest
                <a href=""
                    class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold hover:shadow-2xl hover:shadow-white/50 transform hover:-translate-y-1 transition-all">
                    Daftar Sekarang - Gratis!
                </a>
            @else
                <a href="{{ route('books.index') }}"
                    class="inline-block px-8 py-4 bg-white text-indigo-600 rounded-xl font-bold hover:shadow-2xl hover:shadow-white/50 transform hover:-translate-y-1 transition-all">
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
