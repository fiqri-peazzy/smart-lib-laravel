@extends('layouts.app')

@section('title', 'Membaca: ' . $collection->title)

@section('content')
<div class="bg-gray-900 min-h-screen flex flex-col" x-data="{ loading: true }">
    <!-- Reader Header -->
    <div class="bg-gray-800/80 backdrop-blur-md border-b border-gray-700 px-4 py-3 sticky top-0 z-50 flex items-center justify-between">
        <div class="flex items-center space-x-3 overflow-hidden">
            <a href="{{ route('digital.show', $collection) }}" class="p-2 hover:bg-gray-700 rounded-xl text-gray-400 hover:text-white transition-all">
                <i class="bi bi-chevron-left text-xl"></i>
            </a>
            <div class="overflow-hidden">
                <h1 class="text-white font-bold truncate text-xs md:text-sm leading-tight">{{ $collection->title }}</h1>
                <p class="text-[10px] text-gray-400 truncate opacity-70">{{ $collection->author }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <a href="{{ route('digital.download', $collection) }}" class="hidden sm:flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-indigo-500/20">
                <i class="bi bi-download mr-2"></i> Unduh
            </a>
            <button @click="toggleFullScreen()" class="p-2 hover:bg-gray-700 rounded-xl text-gray-400 hover:text-white transition-all">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>
        </div>
    </div>

    <!-- Reader Area -->
    <div class="flex-1 relative bg-gray-950 flex justify-center overflow-hidden">
        <!-- Loading Indicator -->
        <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-950 z-40">
            <div class="relative w-20 h-20">
                <div class="absolute inset-0 border-4 border-indigo-500/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="mt-4 text-gray-400 text-sm font-medium animate-pulse">Menyiapkan dokumen...</p>
        </div>

        @php
            $extension = pathinfo($collection->file_path, PATHINFO_EXTENSION);
            $fileUrl = asset('storage/' . $collection->file_path);
        @endphp

        @if(strtolower($extension) === 'pdf')
            <iframe 
                src="{{ $fileUrl }}#toolbar=0" 
                class="w-full h-full border-none shadow-2xl"
                style="height: calc(100vh - 64px);"
                allow="fullscreen"
                @load="loading = false"
            ></iframe>
        @else
            <div class="flex flex-col items-center justify-center text-center p-6 text-white h-full" x-init="loading = false">
                <div class="w-20 h-20 bg-gray-800 rounded-3xl flex items-center justify-center mb-6 shadow-xl border border-gray-700">
                    <i class="bi bi-file-earmark-text text-3xl text-indigo-400"></i>
                </div>
                <h2 class="text-xl font-bold mb-3">Format tidak mendukung preview</h2>
                <p class="text-gray-400 mb-8 max-w-xs text-sm">File dengan format .{{ $extension }} saat ini hanya bisa dibuka dengan mengunduh.</p>
                <a href="{{ route('digital.download', $collection) }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-xl shadow-indigo-500/30 flex items-center">
                    <i class="bi bi-download mr-2 text-lg"></i> Unduh File Sekarang
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Hide layout elements during reading */
    nav, footer {
        display: none !important;
    }
    main {
        padding: 0 !important;
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
</script>
@endpush

@endsection
