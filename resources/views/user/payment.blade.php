@extends('layouts.app')

@section('title', 'Pembayaran Denda')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Pembayaran Denda</h1>
                    <p class="text-indigo-100">Kelola pembayaran denda perpustakaan Anda</p>
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
            <!-- Total Unpaid Alert -->
            @if ($totalUnpaid > 0)
                <div class="mb-8 p-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-2xl">
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                            <i class="bi bi-exclamation-triangle-fill text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-red-900 dark:text-red-300 mb-2">Total Denda Belum Dibayar
                            </h3>
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-3">
                                Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                            </div>
                            <p class="text-red-800 dark:text-red-300 text-sm">
                                Anda memiliki {{ $unpaidFines->count() }} denda yang belum dibayar. Silakan lakukan
                                pembayaran untuk dapat meminjam buku kembali.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-8 p-6 bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-2xl">
                    <div class="flex items-center">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <i class="bi bi-check-circle-fill text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-green-900 dark:text-green-300 mb-1">Tidak Ada Denda</h3>
                            <p class="text-green-800 dark:text-green-300 text-sm">
                                Anda tidak memiliki denda yang belum dibayar. Terima kasih atas kedisiplinan Anda!
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Payment Instructions -->
            <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-500 rounded-xl p-6">
                <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-4 flex items-center">
                    <i class="bi bi-info-circle-fill mr-2"></i>
                    Cara Melakukan Pembayaran
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Metode Pembayaran:</h4>
                        <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                            <li class="flex items-start">
                                <i class="bi bi-cash-coin mr-2 mt-0.5"></i>
                                <span><strong>Cash:</strong> Bayar langsung di perpustakaan</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-bank mr-2 mt-0.5"></i>
                                <span><strong>Transfer Bank:</strong> Transfer ke rekening perpustakaan, tunjukkan bukti ke
                                    staff</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-qr-code mr-2 mt-0.5"></i>
                                <span><strong>QRIS/E-Wallet:</strong> Staff akan generate QR code untuk pembayaran
                                    digital</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Langkah-langkah:</h4>
                        <ol class="space-y-2 text-sm text-blue-800 dark:text-blue-300 list-decimal list-inside">
                            <li>Datang ke perpustakaan pada jam operasional</li>
                            <li>Tunjukkan halaman ini atau sebutkan NIM Anda</li>
                            <li>Staff akan memproses pembayaran</li>
                            <li>Setelah lunas, Anda dapat meminjam buku kembali</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-8">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" role="tablist">
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="unpaid" onclick="switchTab('unpaid')">
                            <i class="bi bi-exclamation-circle mr-2"></i>
                            Belum Dibayar
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                {{ $unpaidFines->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="paid" onclick="switchTab('paid')">
                            <i class="bi bi-check-circle mr-2"></i>
                            Sudah Dibayar
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                {{ $paidFines->count() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Contents -->
            <div id="tab-contents">
                <!-- Unpaid Tab -->
                <div id="unpaid-tab" class="tab-content">
                    @if ($unpaidFines->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-check-circle text-3xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Denda</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda tidak memiliki denda yang belum dibayar</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($unpaidFines as $fine)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border-2 border-red-500">
                                    <!-- Header -->
                                    <div class="bg-gradient-to-r from-red-500 to-orange-600 text-white p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold uppercase tracking-wider">Denda #{{
                                                $fine->id }}</span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 bg-white/20 rounded-full text-xs font-bold">
                                                <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                                                Belum Dibayar
                                            </span>
                                        </div>
                                        <div class="text-2xl font-bold">
                                            Rp {{ number_format($fine->amount, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Body -->
                                    <div class="p-5 space-y-4">
                                        <!-- Book Info -->
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Buku</div>
                                            <div class="font-semibold text-gray-900 dark:text-white line-clamp-2">
                                                {{ $fine->loan->bookItem->book->title }}
                                            </div>
                                        </div>

                                        <!-- Loan Info -->
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Loan ID</div>
                                                <div class="font-mono text-gray-900 dark:text-white">#{{
                                                    $fine->loan_id }}</div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Terlambat</div>
                                                <div class="font-semibold text-red-600 dark:text-red-400">
                                                    {{ $fine->days_overdue }} hari
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Created Date -->
                                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <i class="bi bi-calendar-event mr-2"></i>
                                                Dibuat: {{ $fine->created_at->format('d M Y') }}
                                            </div>
                                        </div>

                                        <!-- Warning -->
                                        <div
                                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-lg p-3">
                                            <p class="text-xs text-yellow-800 dark:text-yellow-300 flex items-start">
                                                <i class="bi bi-info-circle-fill mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span>Segera bayar untuk dapat meminjam buku kembali</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Paid Tab -->
                <div id="paid-tab" class="tab-content hidden">
                    @if ($paidFines->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-clock-history text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Riwayat</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda belum memiliki riwayat pembayaran denda</p>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                ID</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Buku</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Jumlah</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Tgl Bayar</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Metode</th>
                                            <th
                                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($paidFines as $fine)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-white">
                                                    #{{ $fine->id }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">
                                                        {{ $fine->loan->bookItem->book->title }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Loan #{{ $fine->loan_id }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                                    Rp {{ number_format($fine->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $fine->paid_at ? $fine->paid_at->format('d M Y') : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst($fine->payment_method ?? '-') }}
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if ($fine->status === 'paid')
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                            <i class="bi bi-check-circle-fill mr-1"></i>
                                                            Lunas
                                                        </span>
                                                    @elseif($fine->status === 'waived')
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                            <i class="bi bi-info-circle-fill mr-1"></i>
                                                            Dibebaskan
                                                        </span>
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
            </div>
        </div>
    </section>

    <!-- Tab Switching Script -->
    <script>
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-400',
                    'dark:text-indigo-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300',
                    'dark:text-gray-400');
            });

            document.getElementById(tabName + '-tab').classList.remove('hidden');

            const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                'hover:border-gray-300', 'dark:text-gray-400');
            activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'dark:border-indigo-400',
                'dark:text-indigo-400');
        }

        document.addEventListener('DOMContentLoaded', function() {
            switchTab('unpaid');
        });
    </script>
@endsection