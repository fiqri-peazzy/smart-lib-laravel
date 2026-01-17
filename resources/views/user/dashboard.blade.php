@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang, {{ $user->name }}!</h1>
            <p class="text-gray-600 dark:text-gray-400">Berikut adalah ringkasan aktivitas perpustakaan Anda.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Active Loans -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="bi bi-book text-2xl"></i>
                    </div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Aktif</span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_loans'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Peminjaman Aktif</div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <i class="bi bi-bookmark text-2xl"></i>
                    </div>
                    <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Pending</span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_bookings'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Booking Menunggu</div>
            </div>

            <!-- Total Fines -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center text-red-600 dark:text-red-400">
                        <i class="bi bi-exclamation-circle text-2xl"></i>
                    </div>
                    <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">Denda</span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">Rp
                    {{ number_format($stats['total_fines'], 0, ',', '.') }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Denda</div>
            </div>

            <!-- Credit Score -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400">
                        <i class="bi bi-star text-2xl"></i>
                    </div>
                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Score</span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->credit_score }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Credit Score</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Loans Table -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Peminjaman Terakhir</h3>
                        <a href="{{ route('loans.my-loans') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Buku
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl
                                        Pinjam</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Tenggat</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($recentLoans as $loan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded shadow-sm object-cover"
                                                        src="{{ $loan->bookItem->book->cover_url }}" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $loan->bookItem->book->title }}</div>
                                                    <div class="text-xs text-gray-500">{{ $loan->bookItem->barcode }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $loan->loan_date ? $loan->loan_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $loan->due_date ? $loan->due_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClasses = [
                                                    'active' =>
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                    'overdue' =>
                                                        'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                    'returned' =>
                                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                    'extended' =>
                                                        'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                                    'pending_pickup' =>
                                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses[$loan->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($loan->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                            Belum ada riwayat peminjaman.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar / Profiles -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profil Saya</h3>
                    <div class="flex items-center mb-6">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                            class="w-16 h-16 rounded-2xl ring-4 ring-indigo-50 dark:ring-indigo-900/20">
                        <div class="ml-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->nim }}</div>
                            <div class="mt-1">
                                <span
                                    class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded">
                                    {{ $user->role_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Email</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Program Studi</span>
                            <span
                                class="text-gray-900 dark:text-white font-medium">{{ $user->major?->name ?? 'Fasilkom Ichsan' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Max Pinjam</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $user->max_loans }} Buku</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('profile') }}"
                            class="block w-full text-center px-4 py-2 border border-indigo-600 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all text-sm font-medium">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
