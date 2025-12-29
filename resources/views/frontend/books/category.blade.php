@extends('layouts.app')

@section('title', 'Kategori: ' . $category->name)

@section('content')
    <!-- Category Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Breadcrumb -->
                <nav class="mb-6">
                    <ol class="flex items-center justify-center space-x-2 text-sm text-indigo-200">
                        <li><a href="{{ route('home') }}" class="hover:text-white"><i class="bi bi-house-door"></i> Home</a></li>
                        <li><i class="bi bi-chevron-right text-xs"></i></li>
                        <li><a href="{{ route('books.index') }}" class="hover:text-white">Books</a></li>
                        <li><i class="bi bi-chevron-right text-xs"></i></li>
                        <li class="text-white font-medium">{{ $category->name }}</li>
                    </ol>
                </nav>

                <!-- Category Badge -->
                <div class="inline-flex items-center justify-center mb-4">
                    <span class="inline-flex items-center px-6 py-3 rounded-full text-lg font-bold text-white bg-white/20 backdrop-blur-sm">
                        <i class="bi bi-tag-fill mr-2"></i>
                        {{ $category->name }}
                    </span>
                </div>

                <!-- Category Title & Description -->
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Kategori: {{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-xl text-indigo-100 max-w-3xl mx-auto mb-8">{{ $category->description }}</p>
                @endif

                <!-- Stats -->
                <div class="flex items-center justify-center gap-8 text-indigo-100">
                    <div class="flex items-center">
                        <i class="bi bi-book mr-2 text-2xl"></i>
                        <div class="text-left">
                            <div class="text-2xl font-bold">{{ $books->total() }}</div>
                            <div class="text-sm">Total Buku</div>
                        </div>
                    </div>
                    <div class="w-px h-12 bg-white/30"></div>
                    <div class="flex items-center">
                        <i class="bi bi-check-circle mr-2 text-2xl"></i>
                        <div class="text-left">
                            <div class="text-2xl font-bold">{{ $books->where('available_stock', '>', 0)->count() }}</div>
                            <div class="text-sm">Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Books Grid -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                <!-- Results Info -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $books->firstItem() ?? 0 }}-{{ $books->lastItem() ?? 0 }}</span>
                    dari <span class="font-semibold text-gray-900 dark:text-white">{{ $books->total() }}</span>
                    buku dalam kategori <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $category->name }}</span>
                </div>

                <!-- Back to All Books -->
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg shadow hover:shadow-lg transition-all">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Lihat Semua Kategori
                </a>
            </div>

            @if ($books->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <i class="bi bi-inbox text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Buku</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Belum ada buku dalam kategori <strong>{{ $category->name }}</strong></p>
                    <div class="flex items-center justify-center gap-4">
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg">
                            <i class="bi bi-collection mr-2"></i>
                            Browse Semua Buku
                        </a>
                    </div>
                </div>
            @else
                <!-- Books Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach ($books as $book)
                        <article class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <!-- Book Cover -->
                                <div class="relative aspect-w-16 aspect-h-9 bg-gradient-to-br from-indigo-500 to-purple-600 overflow-hidden">
                                    @if ($book->cover_image)
                                        <img src="{{ $book->cover_url }}" 
                                             alt="{{ $book->title }}"
                                             class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-48 flex items-center justify-center text-white">
                                            <i class="bi bi-book text-6xl opacity-50"></i>
                                        </div>
                                    @endif

                                    <!-- Availability Badge -->
                                    <div class="absolute top-4 right-4">
                                        @if ($book->available_stock > 0)
                                            <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                <i class="bi bi-check-circle mr-1"></i>
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                <i class="bi bi-x-circle mr-1"></i>
                                                Dipinjam
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Category Badge (Top Left) -->
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg bg-white/90 backdrop-blur-sm" 
                                              style="color: {{ $category->color }};">
                                            <i class="bi bi-tag-fill mr-1"></i>
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Book Info -->
                                <div class="p-5">
                                    <!-- Additional Categories (if any) -->
                                    @if($book->categories->count() > 1)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach ($book->categories->where('id', '!=', $category->id)->take(2) as $cat)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg"
                                                      style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                                    <i class="bi bi-tag mr-1"></i>
                                                    {{ $cat->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Title -->
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $book->title }}
                                    </h3>

                                    <!-- Author -->
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex items-center">
                                        <i class="bi bi-person mr-2"></i>
                                        {{ $book->author }}
                                    </p>

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <i class="bi bi-geo-alt mr-1"></i>
                                            {{ $book->rack_location ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm font-semibold"
                                             :class="{
                                                 'text-green-600': {{ $book->available_stock }} > 0,
                                                 'text-red-600': {{ $book->available_stock }} === 0
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
        </div>
    </section>

    <!-- Related Categories (Optional) -->
    @php
        $relatedCategories = \App\Models\BookCategory::where('id', '!=', $category->id)
            ->where('is_active', true)
            ->withCount('books')
            ->having('books_count', '>', 0)
            ->take(6)
            ->get();
    @endphp

    @if($relatedCategories->isNotEmpty())
        <section class="py-12 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Kategori Lainnya</h2>
                    <p class="text-gray-600 dark:text-gray-400">Jelajahi koleksi buku dari kategori lain</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($relatedCategories as $cat)
                        <a href="{{ route('books.category', $cat) }}" 
                           class="group p-6 bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 transition-colors"
                                     style="background-color: {{ $cat->color }}20;">
                                    <i class="bi bi-tag-fill text-2xl" style="color: {{ $cat->color }};"></i>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $cat->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $cat->books_count }} buku
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection