@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Peminjaman Saya</h1>
                    <p class="text-indigo-100">Kelola semua peminjaman buku Anda</p>
                </div>
                <a href="{{ route('dashboard') }}"
                    class="hidden md:inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-colors">
                    <i class="bi bi-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-500 rounded-xl flex items-start">
                    <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()"
                        class="text-green-600 hover:text-green-800 dark:text-green-400">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-500 rounded-xl flex items-start">
                    <i class="bi bi-exclamation-circle-fill text-red-600 dark:text-red-400 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 dark:text-red-400">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            <!-- Tabs -->
            <div class="mb-8">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" role="tablist">
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="pending" onclick="switchTab('pending')">
                            <i class="bi bi-hourglass-split mr-2"></i>
                            Pending Pickup
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                {{ $pendingPickupLoans->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="active" onclick="switchTab('active')">
                            <i class="bi bi-book mr-2"></i>
                            Aktif
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $activeLoans->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="overdue" onclick="switchTab('overdue')">
                            <i class="bi bi-exclamation-triangle mr-2"></i>
                            Terlambat
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                {{ $overdueLoans->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="history" onclick="switchTab('history')">
                            <i class="bi bi-clock-history mr-2"></i>
                            Riwayat
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                {{ $historyLoans->total() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Contents -->
            <div id="tab-contents">
                <!-- Pending Pickup Tab -->
                <div id="pending-tab" class="tab-content">
                    @if ($pendingPickupLoans->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-hourglass-split text-3xl text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Request Pending</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Anda belum memiliki request peminjaman yang
                                menunggu pickup</p>
                            <a href="{{ route('books.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="bi bi-collection mr-2"></i>
                                Browse Buku
                            </a>
                        </div>
                    @else
                        <!-- Alert -->
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-xl p-4 mb-6 flex items-start">
                            <i
                                class="bi bi-exclamation-triangle-fill text-yellow-600 dark:text-yellow-400 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-yellow-900 dark:text-yellow-300 font-bold mb-1">Segera Ambil Buku Anda!</h4>
                                <p class="text-yellow-800 dark:text-yellow-300 text-sm">
                                    Anda memiliki {{ $pendingPickupLoans->count() }} buku yang menunggu diambil. Datang ke
                                    perpustakaan dan tunjukkan QR code ke staff.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($pendingPickupLoans as $loan)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border-2 border-yellow-500">
                                    <!-- Book Cover -->
                                    <div class="relative h-48 bg-gradient-to-br from-yellow-500 to-orange-600">
                                        @if ($loan->bookItem->book->cover_image)
                                            <img src="{{ $loan->bookItem->book->cover_url }}"
                                                alt="{{ $loan->bookItem->book->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white">
                                                <i class="bi bi-book text-6xl opacity-50"></i>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-lg animate-pulse">
                                                <i class="bi bi-hourglass-split mr-1"></i>
                                                Pending Pickup
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-5">
                                        <!-- Title -->
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                            {{ $loan->bookItem->book->title }}
                                        </h3>

                                        <!-- Info -->
                                        <div class="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <i class="bi bi-calendar-plus mr-2"></i>
                                                <span>Request: {{ $loan->requested_at->format('d M Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-clock mr-2"></i>
                                                <span>Ambil sebelum:
                                                    {{ $loan->pickup_deadline->format('d M Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-hourglass-bottom mr-2"></i>
                                                @php
                                                    $daysLeft = $loan->days_until_pickup;
                                                @endphp
                                                @if ($daysLeft > 1)
                                                    <span class="text-green-600 dark:text-green-400 font-medium">
                                                        {{ $daysLeft }} hari lagi
                                                    </span>
                                                @elseif($daysLeft == 1)
                                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">
                                                        Besok expired!
                                                    </span>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400 font-medium">
                                                        Expired hari ini!
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-upc-scan mr-2"></i>
                                                <span class="font-mono text-xs">{{ $loan->bookItem->barcode }}</span>
                                            </div>
                                        </div>

                                        <!-- QR Code Preview -->
                                        @if ($loan->bookItem->qr_code_url)
                                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-semibold">QR
                                                    Code Anda:</p>
                                                <img src="{{ $loan->bookItem->qr_code_url }}" alt="QR Code"
                                                    class="w-32 h-32 mx-auto object-contain bg-white rounded-lg p-2">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Tunjukkan ke staff
                                                    perpustakaan</p>
                                            </div>
                                        @endif

                                        <!-- Warning -->
                                        <div
                                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-lg p-3">
                                            <p class="text-xs text-yellow-800 dark:text-yellow-300 flex items-start">
                                                <i class="bi bi-info-circle-fill mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span>Request akan otomatis dibatalkan jika tidak diambil dalam
                                                    {{ $daysLeft > 0 ? $daysLeft : 0 }} hari</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Active Loans Tab -->
                <div id="active-tab" class="tab-content">
                    @if ($activeLoans->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-book text-3xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Peminjaman Aktif
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Anda belum memiliki peminjaman buku yang aktif
                            </p>
                            <a href="{{ route('books.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="bi bi-collection mr-2"></i>
                                Browse Buku
                            </a>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Buku</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Tgl Pinjam</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Jatuh Tempo</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Sisa Waktu</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($activeLoans as $loan)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <img src="{{ $loan->bookItem->book->cover_url }}"
                                                            alt="{{ $loan->bookItem->book->title }}"
                                                            class="w-12 h-16 object-cover rounded shadow-sm">
                                                        <div class="ml-4">
                                                            <div
                                                                class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">
                                                                {{ $loan->bookItem->book->title }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                <i class="bi bi-upc-scan mr-1"></i>
                                                                {{ $loan->bookItem->barcode }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->loan_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->due_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $daysLeft = $loan->days_until_due;
                                                        $isOverdue = $daysLeft < 0;
                                                        $absdays = abs($daysLeft);
                                                    @endphp
                                                    @if ($isOverdue)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                            <i class="bi bi-exclamation-circle-fill mr-1"></i>
                                                            Terlambat {{ $absdays }} hari
                                                        </span>
                                                    @elseif($daysLeft <= 3)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                            <i class="bi bi-hourglass-split mr-1"></i>
                                                            {{ $daysLeft }} hari lagi
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                            <i class="bi bi-check-circle mr-1"></i>
                                                            {{ $daysLeft }} hari lagi
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($loan->status === 'extended')
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                                            <i class="bi bi-arrow-repeat mr-1"></i>
                                                            Diperpanjang
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                            <i class="bi bi-circle-fill mr-1 text-[8px]"></i>
                                                            Aktif
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if ($loan->canBeExtended())
                                                        <form action="{{ route('loans.extend', $loan) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                                                <i class="bi bi-arrow-clockwise mr-1"></i>
                                                                Perpanjang
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button disabled
                                                            class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed"
                                                            title="Tidak dapat diperpanjang">
                                                            <i class="bi bi-x-circle mr-1"></i>
                                                            Tidak Bisa
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Overdue Loans Tab -->
                <div id="overdue-tab" class="tab-content hidden">
                    @if ($overdueLoans->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-check-circle text-3xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Keterlambatan</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda tidak memiliki peminjaman yang terlambat</p>
                        </div>
                    @else
                        <!-- Warning Alert -->
                        <div
                            class="bg-red-50 dark:bg-red-900/20 border border-red-500 rounded-xl p-4 mb-6 flex items-start">
                            <i
                                class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-red-900 dark:text-red-300 font-bold mb-1">Perhatian!</h4>
                                <p class="text-red-800 dark:text-red-300 text-sm">
                                    Anda memiliki {{ $overdueLoans->count() }} peminjaman yang terlambat. Segera kembalikan
                                    untuk menghindari denda bertambah (Rp 1.000/hari, max Rp 50.000).
                                </p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Buku</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Jatuh Tempo</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Terlambat</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Denda</th>
                                            <th
                                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($overdueLoans as $loan)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <img src="{{ $loan->bookItem->book->cover_url }}"
                                                            alt="{{ $loan->bookItem->book->title }}"
                                                            class="w-12 h-16 object-cover rounded shadow-sm">
                                                        <div class="ml-4">
                                                            <div
                                                                class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">
                                                                {{ $loan->bookItem->book->title }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                <i class="bi bi-upc-scan mr-1"></i>
                                                                {{ $loan->bookItem->barcode }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->due_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                        <i class="bi bi-hourglass-bottom mr-1"></i>
                                                        {{ $loan->getDaysOverdue() }} hari
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                                        Rp {{ number_format($loan->calculateFine(), 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                        <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                                                        Overdue
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- History Tab -->
                <div id="history-tab" class="tab-content hidden">
                    @if ($historyLoans->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-clock-history text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Riwayat</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda belum memiliki riwayat peminjaman</p>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Buku</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Tgl Pinjam</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Tgl Kembali</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Durasi</th>
                                            <th
                                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($historyLoans as $loan)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <img src="{{ $loan->bookItem->book->cover_url }}"
                                                            alt="{{ $loan->bookItem->book->title }}"
                                                            class="w-12 h-16 object-cover rounded shadow-sm">
                                                        <div class="ml-4">
                                                            <div
                                                                class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">
                                                                {{ $loan->bookItem->book->title }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                <i class="bi bi-upc-scan mr-1"></i>
                                                                {{ $loan->bookItem->barcode }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->loan_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->return_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $loan->loan_date->diffInDays($loan->return_date) }} hari
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if ($loan->return_date <= $loan->due_date)
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                            <i class="bi bi-check-circle-fill mr-1"></i>
                                                            Tepat Waktu
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                            <i class="bi bi-clock-fill mr-1"></i>
                                                            Terlambat
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                {{ $historyLoans->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Tab Switching Script -->
    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Remove active state from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-400',
                    'dark:text-indigo-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                    'hover:border-gray-300',
                    'dark:text-gray-400');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');

            // Add active state to clicked button
            const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                'hover:border-gray-300', 'dark:text-gray-400');
            activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-400',
                'dark:text-indigo-400');
        }

        // Initialize first tab as active
        document.addEventListener('DOMContentLoaded', function() {
            switchTab('pending'); // Changed from 'active' to 'pending'
        });
    </script>
@endsection
