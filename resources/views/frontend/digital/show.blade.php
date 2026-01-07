@extends('layouts.app')

@section('title', $collection->title . ' - Digital Library')

@section('content')
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <!-- Breadcrumbs & Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <nav class="flex items-center text-sm font-medium mb-8">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">Home</a>
                <i class="bi bi-chevron-right mx-3 text-gray-300"></i>
                <a href="{{ route('digital.index') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">Digital Library</a>
                <i class="bi bi-chevron-right mx-3 text-gray-300"></i>
                <span class="text-indigo-600 dark:text-indigo-400 truncate">{{ $collection->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Left Column: Main Detail -->
                <div class="lg:col-span-8">
                    <!-- Title Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden relative">
                        <!-- Decorative element -->
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-50 dark:bg-indigo-900/20 rounded-full blur-3xl"></div>

                        <div class="relative z-10">
                            <div class="flex flex-wrap items-center gap-3 mb-6">
                                <span class="px-4 py-1.5 bg-indigo-600 text-white rounded-full text-xs font-bold uppercase tracking-widest">
                                    {{ $collection->type }}
                                </span>
                                <span class="px-4 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-bold flex items-center">
                                    <i class="bi bi-calendar-event mr-2"></i> {{ $collection->year }}
                                </span>
                                <span class="px-4 py-1.5 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-full text-xs font-bold flex items-center">
                                    <i class="bi bi-eye mr-2"></i> {{ number_format($collection->view_count) }} views
                                </span>
                            </div>

                            <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                                {{ $collection->title }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-6 text-gray-600 dark:text-gray-400">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mr-3 text-indigo-600">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-wider opacity-60 leading-none mb-1">Penulis</div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $collection->author }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-3 text-purple-600">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-wider opacity-60 leading-none mb-1">Program Studi</div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $collection->major->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Abstract/Description -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 mb-8">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                            <i class="bi bi-text-paragraph mr-3 text-indigo-500"></i>
                            Abstrak / Deskripsi
                        </h2>
                        <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed italic">
                            {!! nl2br(e($collection->description)) !!}
                        </div>
                        
                        <!-- Keywords -->
                        @if($collection->keywords_array)
                            <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 opacity-70 tracking-widest uppercase">Kata Kunci</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($collection->keywords_array as $keyword)
                                        <a href="{{ route('digital.index', ['search' => $keyword]) }}" class="px-4 py-2 bg-gray-50 dark:bg-gray-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-300 rounded-xl text-xs font-medium transition-all">
                                            #{{ $keyword }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Actions & Meta -->
                <div class="lg:col-span-4">
                    <!-- Action Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl border border-gray-100 dark:border-gray-700 mb-8 sticky top-24">
                        <div class="space-y-4">
                            @auth
                                <a href="{{ route('digital.read', $collection) }}" target="_blank" class="w-full flex items-center justify-center py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-indigo-500/30">
                                    <i class="bi bi-book-half mr-3 whitespace-nowrap"></i> Baca Sekarang
                                </a>
                                <a href="{{ route('digital.download', $collection) }}" class="w-full flex items-center justify-center py-4 bg-white dark:bg-gray-750 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-2xl font-bold transition-all">
                                    <i class="bi bi-download mr-3"></i> Unduh File
                                </a>
                            @else
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 text-center">
                                    <i class="bi bi-lock-fill text-amber-500 text-3xl mb-3 block"></i>
                                    <h4 class="text-amber-800 dark:text-amber-400 font-bold mb-2">Akses Terbatas</h4>
                                    <p class="text-xs text-amber-700 dark:text-amber-500 mb-4 lh-relaxed">Silakan login untuk dapat membaca atau mengunduh koleksi digital ini.</p>
                                    <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all">
                                        Login Sekarang
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-6">Informasi File</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500 flex items-center uppercase font-bold tracking-widest opacity-70">
                                        <i class="bi bi-file-earmark-code mr-2"></i> Format
                                    </div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $collection->file_type ?? 'PDF' }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500 flex items-center uppercase font-bold tracking-widest opacity-70">
                                        <i class="bi bi-hdd mr-2"></i> Ukuran
                                    </div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $collection->file_size_readable }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500 flex items-center uppercase font-bold tracking-widest opacity-70">
                                        <i class="bi bi-download mr-2"></i> Diunduh
                                    </div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white">{{ number_format($collection->download_count) }} kali</div>
                                </div>
                                @if($collection->isbn)
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-gray-500 flex items-center uppercase font-bold tracking-widest opacity-70">
                                            <i class="bi bi-hash mr-2"></i> ISBN
                                        </div>
                                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $collection->isbn }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Share -->
                        <div class="mt-8 pt-6">
                            <div class="flex items-center justify-center space-x-4">
                                <button class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i class="bi bi-facebook"></i>
                                </button>
                                <button class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i class="bi bi-twitter-x"></i>
                                </button>
                                <button class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i class="bi bi-linkedin"></i>
                                </button>
                                <button onclick="navigator.clipboard.writeText(window.location.href)" class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items -->
            @if(!$relatedItems->isEmpty())
                <div class="mt-20">
                    <div class="flex items-center justify-between mb-10 overflow-hidden">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white">Koleksi Terkait</h2>
                        <a href="{{ route('digital.index', ['type' => $collection->type]) }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua</a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedItems as $item)
                            <a href="{{ route('digital.show', $item) }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4 transition-transform group-hover:scale-110">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 leading-snug">{{ $item->title }}</h3>
                                <p class="text-[10px] text-gray-500 mb-4 flex items-center">
                                    <i class="bi bi-person mr-1"></i> {{ $item->author }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-indigo-600">{{ $item->year }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $item->file_size_readable }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
