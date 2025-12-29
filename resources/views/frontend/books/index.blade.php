@extends('layouts.app')

@section('title', 'Browse Books')

@section('content')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Katalog Buku</h1>
                <p class="text-xl text-indigo-100 mb-8">Jelajahi koleksi lengkap perpustakaan Fasilkom</p>

                <!-- Search Bar -->
                <form action="{{ route('books.index') }}" method="GET" class="max-w-3xl mx-auto">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                                <i class="bi bi-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul, penulis, atau ISBN..."
                                class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 focus:ring-4 focus:ring-indigo-300 border-0">
                        </div>
                        <button type="submit"
                            class="px-8 py-4 bg-white text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 transition-colors">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Filters & Books Grid -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                                <i class="bi bi-funnel mr-2"></i>
                                Filter
                            </h3>
                            @if (request()->hasAny(['category', 'year', 'search']))
                                <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:underline">
                                    Reset
                                </a>
                            @endif
                        </div>

                        <form action="{{ route('books.index') }}" method="GET" class="space-y-6">
                            <!-- Preserve search -->
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="bi bi-tag mr-1"></i> Kategori
                                </label>
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach ($categories as $cat)
                                        <label
                                            class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="radio" name="category" value="{{ $cat->id }}"
                                                {{ request('category') == $cat->id ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex-1">
                                                {{ $cat->name }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                {{ $cat->books_count }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Year Filter -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="bi bi-calendar-event mr-1"></i> Tahun Terbit
                                </label>
                                <select name="year" onchange="this.form.submit()"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="bi bi-sort-down mr-1"></i> Urutkan
                                </label>
                                <select name="sort" onchange="this.form.submit()"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul (A-Z)
                                    </option>
                                    <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Penulis
                                        (A-Z)</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Populer
                                    </option>
                                </select>
                            </div>
                        </form>

                        <!-- Stats -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Buku</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $books->total() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Books Grid -->
                <main class="lg:w-3/4">
                    <!-- Results Info -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Menampilkan <span
                                class="font-semibold text-gray-900 dark:text-white">{{ $books->firstItem() ?? 0 }}-{{ $books->lastItem() ?? 0 }}</span>
                            dari <span class="font-semibold text-gray-900 dark:text-white">{{ $books->total() }}</span>
                            buku
                        </div>
                    </div>

                    @if ($books->isEmpty())
                        <!-- Empty State -->
                        <div class="text-center py-16">
                            <div
                                class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="bi bi-book text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak ada buku ditemukan</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Coba ubah filter atau kata kunci pencarian</p>
                            <a href="{{ route('books.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="bi bi-arrow-clockwise mr-2"></i>
                                Reset Filter
                            </a>
                        </div>
                    @else
                        <!-- Books Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($books as $book)
                                <article
                                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                                    <a href="{{ route('books.show', $book) }}" class="block">
                                        <!-- Book Cover -->
                                        <div
                                            class="relative aspect-w-16 aspect-h-9 bg-gradient-to-br from-indigo-500 to-purple-600 overflow-hidden">
                                            @if ($book->cover_image)
                                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                                                    class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-48 flex items-center justify-center text-white">
                                                    <i class="bi bi-book text-6xl opacity-50"></i>
                                                </div>
                                            @endif

                                            <!-- Availability Badge -->
                                            <div class="absolute top-4 right-4">
                                                @if ($book->available_stock > 0)
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                                        <i class="bi bi-check-circle mr-1"></i>
                                                        Tersedia
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                                        <i class="bi bi-x-circle mr-1"></i>
                                                        Dipinjam
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Book Info -->
                                        <div class="p-5">
                                            <!-- Categories -->
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                @foreach ($book->categories->take(2) as $cat)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg"
                                                        style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                                        <i class="bi bi-tag-fill mr-1"></i>
                                                        {{ $cat->name }}
                                                    </span>
                                                @endforeach
                                            </div>

                                            <!-- Title -->
                                            <h3
                                                class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $book->title }}
                                            </h3>

                                            <!-- Author -->
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex items-center">
                                                <i class="bi bi-person mr-2"></i>
                                                {{ $book->author }}
                                            </p>

                                            <!-- Footer -->
                                            <div
                                                class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                    <i class="bi bi-geo-alt mr-1"></i>
                                                    {{ $book->rack_location ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm font-semibold"
                                                    :class="{
                                                        'text-green-600': {{ $book->available_stock }},
                                                        'text-red-600': !
                                                            {{ $book->available_stock }}
                                                    }">
                                                    <i class="bi bi-box mr-1"></i>
                                                    {{ $book->available_stock }}/{{ $book->total_stock }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $books->links() }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>

@endsection
