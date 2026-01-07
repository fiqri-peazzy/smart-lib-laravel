@extends('layouts.app')

@section('title', 'Digital Library - Koleksi Perpustakaan')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-br from-indigo-700 via-purple-700 to-indigo-900 text-white py-16 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500 rounded-full mix-blend-overlay filter blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Perpustakaan Digital</h1>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">Akses ribuan karya ilmiah, skripsi, e-book, dan jurnal penelitian secara online.</p>

                <!-- Search Bar -->
                <form action="{{ route('digital.index') }}" method="GET" class="max-w-3xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                                <i class="bi bi-search text-gray-400"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul, penulis, kata kunci..."
                                class="w-full pl-12 pr-4 py-4 rounded-2xl text-gray-900 focus:ring-4 focus:ring-indigo-300 border-0 shadow-xl">
                        </div>
                        <button type="submit"
                            class="px-8 py-4 bg-indigo-500 text-white rounded-2xl font-bold hover:bg-indigo-400 transition-all shadow-xl flex items-center justify-center">
                            Cari Koleksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Filters & Results -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6 border border-gray-100 dark:border-gray-700 sticky top-24">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                                <i class="bi bi-sliders mr-2 text-indigo-500"></i>
                                Filter
                            </h3>
                            @if (request()->anyFilled(['type', 'major', 'search']))
                                <a href="{{ route('digital.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    RESET
                                </a>
                            @endif
                        </div>

                        <form action="{{ route('digital.index') }}" method="GET" class="space-y-8">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Type Filter -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 tracking-wider uppercase opacity-70">
                                    Jenis Koleksi
                                </label>
                                <div class="grid grid-cols-1 gap-2">
                                    <button type="submit" name="type" value="" 
                                        class="flex items-center px-4 py-2 rounded-xl text-sm transition-all {{ !request('type') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-gray-50 dark:bg-gray-750 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                        Semua
                                    </button>
                                    @foreach($types as $type)
                                        <button type="submit" name="type" value="{{ $type }}" 
                                            class="flex items-center px-4 py-2 rounded-xl text-sm transition-all {{ request('type') == $type ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-gray-50 dark:bg-gray-750 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                            {{ ucfirst($type) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Major Filter -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 tracking-wider uppercase opacity-70">
                                    Program Studi
                                </label>
                                <select name="major" onchange="this.form.submit()"
                                    class="w-full rounded-xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:text-gray-300">
                                    <option value="">Semua Program Studi</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" {{ request('major') == $major->id ? 'selected' : '' }}>
                                            {{ $major->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 tracking-wider uppercase opacity-70">
                                    Urutkan
                                </label>
                                <select name="sort" onchange="this.form.submit()"
                                    class="w-full rounded-xl border-gray-100 dark:border-gray-700 dark:bg-gray-900 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:text-gray-300">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                    <option value="downloads" {{ request('sort') == 'downloads' ? 'selected' : '' }}>Banyak Diunduh</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Collection Grid -->
                <main class="lg:w-3/4">
                    <div class="flex items-center justify-between mb-8 overflow-hidden">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            @if(request('search'))
                                Hasil untuk "{{ request('search') }}"
                            @elseif(request('type'))
                                Koleksi: {{ ucfirst(request('type')) }}
                            @else
                                Semua Koleksi Digital
                            @endif
                            <span class="ml-2 text-sm font-normal text-gray-500">({{ $collections->total() }} item)</span>
                        </h2>
                    </div>

                    @if ($collections->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="bi bi-journal-x text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Item tidak ditemukan</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8">Maaf, kami tidak menemukan koleksi yang sesuai dengan kriteria Anda.</p>
                            <a href="{{ route('digital.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all">
                                <i class="bi bi-arrow-left mr-2"></i> Reset Pencarian
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($collections as $item)
                                <article class="group bg-white dark:bg-gray-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                                    <div class="p-6">
                                        <!-- Header Item -->
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 transition-colors">
                                                @php
                                                    $icon = match($item->type) {
                                                        'skripsi' => 'bi-mortarboard',
                                                        'journal' => 'bi-journal-medical',
                                                        'ebook' => 'bi-book',
                                                        'paper' => 'bi-file-earmark-text',
                                                        default => 'bi-journal-text'
                                                    };
                                                @endphp
                                                <i class="bi {{ $icon }} text-2xl"></i>
                                            </div>
                                            <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                                {{ $item->type }}
                                            </span>
                                        </div>

                                        <!-- Content -->
                                        <a href="{{ route('digital.show', $item) }}" class="block group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 leading-tight">
                                                {{ $item->title }}
                                            </h3>
                                        </a>
                                        
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 flex items-center">
                                            <i class="bi bi-person mr-2 text-indigo-400"></i>
                                            {{ $item->author }}
                                        </p>

                                        <div class="flex flex-wrap gap-2 mb-6">
                                            <span class="text-[10px] bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-md font-medium">
                                                {{ $item->year }}
                                            </span>
                                            @if($item->major)
                                                <span class="text-[10px] bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-2 py-1 rounded-md font-medium">
                                                    {{ $item->major->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Footer Meta -->
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                                            <div class="flex space-x-4">
                                                <div class="flex items-center text-[10px] text-gray-400">
                                                    <i class="bi bi-eye mr-1"></i> {{ number_format($item->view_count) }}
                                                </div>
                                                <div class="flex items-center text-[10px] text-gray-400">
                                                    <i class="bi bi-download mr-1"></i> {{ number_format($item->download_count) }}
                                                </div>
                                            </div>
                                            <div class="flex items-center text-[10px] text-gray-400">
                                                <i class="bi bi-hdd mr-1"></i> {{ $item->file_size_readable }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button Overlay -->
                                    <div class="px-6 pb-6 pt-0">
                                        <a href="{{ route('digital.show', $item) }}" class="w-full flex items-center justify-center py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white dark:text-gray-300 rounded-xl text-xs font-bold transition-all duration-300">
                                            Lihat Selengkapnya
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $collections->links() }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>
@endsection
