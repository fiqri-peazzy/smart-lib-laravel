@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Booking Saya</h1>
                    <p class="text-indigo-100">Kelola reservasi buku yang sedang dipinjam orang lain</p>
                </div>
                <a href="{{ route('dashboard') }}"
                    class="hidden md:inline-flex items-center px-4 pyKO-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-colors">
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

            <!-- Info Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-500 rounded-xl p-4 mb-8 flex items-start">
                <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-xl mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="text-blue-900 dark:text-blue-300 font-bold mb-1">Tentang Booking</h4>
                    <p class="text-blue-800 dark:text-blue-300 text-sm">
                        Booking memungkinkan Anda mereservasi buku yang sedang dipinjam. Anda akan diberitahu saat buku
                        tersedia dan memiliki 3 hari untuk mengambilnya.
                        @auth
                            @if (Auth::user()->isDosen())
                                <span class="font-semibold">Sebagai dosen, booking Anda memiliki prioritas lebih
                                    tinggi.</span>
                            @endif
                        @endauth
                    </p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-8">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" role="tablist">
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="pending" onclick="switchTab('pending')">
                            <i class="bi bi-hourglass-split mr-2"></i>
                            Menunggu
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                {{ $pendingBookings->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="notified" onclick="switchTab('notified')">
                            <i class="bi bi-bell mr-2"></i>
                            Tersedia
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                {{ $notifiedBookings->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            data-tab="history" onclick="switchTab('history')">
                            <i class="bi bi-clock-history mr-2"></i>
                            Riwayat
                            <span
                                class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                {{ $historyBookings->total() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Contents -->
            <div id="tab-contents">
                <!-- Pending Bookings Tab -->
                <div id="pending-tab" class="tab-content">
                    @if ($pendingBookings->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-bookmark text-3xl text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Booking Pending</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Anda belum memiliki booking yang sedang
                                menunggu</p>
                            <a href="{{ route('books.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="bi bi-collection mr-2"></i>
                                Browse Buku
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($pendingBookings as $booking)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                    <!-- Book Cover -->
                                    <div class="relative h-48 bg-gradient-to-br from-yellow-500 to-orange-600">
                                        @if ($booking->book->cover_image)
                                            <img src="{{ $booking->book->cover_url }}" alt="{{ $booking->book->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white">
                                                <i class="bi bi-book text-6xl opacity-50"></i>
                                            </div>
                                        @endif

                                        <!-- Priority Badge -->
                                        @if ($booking->is_priority)
                                            <div class="absolute top-3 left-3">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-full shadow-lg">
                                                    <i class="bi bi-star-fill mr-1"></i>
                                                    Priority
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                <i class="bi bi-hourglass-split mr-1"></i>
                                                Pending
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-5">
                                        <!-- Categories -->
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach ($booking->book->categories->take(2) as $cat)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-lg"
                                                    style="background-color: {{ $cat->color }}20; color: {{ $cat->color }};">
                                                    <i class="bi bi-tag mr-1"></i>
                                                    {{ $cat->name }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <!-- Title -->
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                            {{ $booking->book->title }}
                                        </h3>

                                        <!-- Info -->
                                        <div class="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <i class="bi bi-calendar-event mr-2"></i>
                                                <span>Dibooking: {{ $booking->booking_date->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-clock mr-2"></i>
                                                <span>Expired: {{ $booking->expires_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-hourglass-bottom mr-2"></i>
                                                @php
                                                    $daysLeft = $booking->days_until_expires;
                                                @endphp
                                                @if ($daysLeft > 0)
                                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">
                                                        {{ $daysLeft }} hari lagi
                                                    </span>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400 font-medium">
                                                        Akan expired hari ini
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <a href="{{ route('books.show', $booking->book) }}"
                                                class="flex-1 text-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                                <i class="bi bi-eye mr-1"></i>
                                                Lihat Buku
                                            </a>
                                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
                                                class="flex-1"
                                                onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">
                                                    <i class="bi bi-x-circle mr-1"></i>
                                                    Batalkan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Notified Bookings Tab -->
                <div id="notified-tab" class="tab-content hidden">
                    @if ($notifiedBookings->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-bell text-3xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Notifikasi</h3>
                            <p class="text-gray-600 dark:text-gray-400">Belum ada buku yang tersedia untuk diambil</p>
                        </div>
                    @else
                        <!-- Alert Box -->
                        <div
                            class="bg-green-50 dark:bg-green-900/20 border border-green-500 rounded-xl p-4 mb-6 flex items-start">
                            <i class="bi bi-bell-fill text-green-600 dark:text-green-400 text-xl mr-3 mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-green-900 dark:text-green-300 font-bold mb-1">Buku Tersedia!</h4>
                                <p class="text-green-800 dark:text-green-300 text-sm">
                                    {{ $notifiedBookings->count() }} buku yang Anda booking sudah tersedia. Segera ambil ke
                                    perpustakaan sebelum expired.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($notifiedBookings as $booking)
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-2 border-green-500">
                                    <!-- Book Cover -->
                                    <div class="relative h-48 bg-gradient-to-br from-green-500 to-emerald-600">
                                        @if ($booking->book->cover_image)
                                            <img src="{{ $booking->book->cover_url }}" alt="{{ $booking->book->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-white">
                                                <i class="bi bi-book text-6xl opacity-50"></i>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg animate-pulse">
                                                <i class="bi bi-check-circle-fill mr-1"></i>
                                                Tersedia
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-5">
                                        <!-- Title -->
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                            {{ $booking->book->title }}
                                        </h3>

                                        <!-- Info -->
                                        <div class="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <i class="bi bi-bell-fill mr-2 text-green-600"></i>
                                                <span>Notified: {{ $booking->notified_at->format('d M Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-clock mr-2"></i>
                                                <span>Ambil sebelum: {{ $booking->expires_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="bi bi-hourglass-bottom mr-2"></i>
                                                @php
                                                    $daysLeft = $booking->days_until_expires;
                                                @endphp
                                                @if ($daysLeft > 1)
                                                    <span class="text-green-600 dark:text-green-400 font-medium">
                                                        {{ $daysLeft }} hari lagi
                                                    </span>
                                                @elseif($daysLeft == 1)
                                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">
                                                        Besok expired
                                                    </span>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400 font-medium">
                                                        Expired hari ini!
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Alert -->
                                        <div
                                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500 rounded-lg p-3 mb-4">
                                            <p class="text-xs text-yellow-800 dark:text-yellow-300 flex items-start">
                                                <i class="bi bi-exclamation-triangle-fill mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span>Segera datang ke perpustakaan untuk mengambil buku ini. Tunjukkan
                                                    notifikasi ini ke staff.</span>
                                            </p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <a href="{{ route('books.show', $booking->book) }}"
                                                class="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors">
                                                <i class="bi bi-eye mr-1"></i>
                                                Detail
                                            </a>
                                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST"
                                                class="flex-1"
                                                onsubmit="return confirm('Yakin tidak jadi mengambil buku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="bi bi-x-circle mr-1"></i>
                                                    Batalkan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- History Tab -->
                <div id="history-tab" class="tab-content hidden">
                    @if ($historyBookings->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                            <div
                                class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-clock-history text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Riwayat</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda belum memiliki riwayat booking</p>
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
                                                Tgl Booking</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                                Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($historyBookings as $booking)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <img src="{{ $booking->book->cover_url }}"
                                                            alt="{{ $booking->book->title }}"
                                                            class="w-10 h-14 object-cover rounded shadow-sm">
                                                        <div class="ml-3">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1">
                                                                {{ $booking->book->title }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $booking->booking_date->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $statusConfig = [
                                                            'fulfilled' => [
                                                                'class' =>
                                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                                'icon' => 'check-circle-fill',
                                                                'text' => 'Terpenuhi',
                                                            ],
                                                            'cancelled' => [
                                                                'class' =>
                                                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                                                'icon' => 'x-circle',
                                                                'text' => 'Dibatalkan',
                                                            ],
                                                            'expired' => [
                                                                'class' =>
                                                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                                'icon' => 'clock',
                                                                'text' => 'Expired',
                                                            ],
                                                        ];
                                                        $config =
                                                            $statusConfig[$booking->status] ??
                                                            $statusConfig['cancelled'];
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $config['class'] }}">
                                                        <i class="bi bi-{{ $config['icon'] }} mr-1"></i>
                                                        {{ $config['text'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                    @if ($booking->status === 'fulfilled')
                                                        Diambil {{ $booking->fulfilled_at?->format('d M Y') }}
                                                    @elseif($booking->status === 'expired')
                                                        Tidak diambil hingga expired
                                                    @else
                                                        {{ $booking->notes ?? 'Dibatalkan oleh user' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                {{ $historyBookings->links() }}
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
            switchTab('pending');
        });
    </script>
@endsection
