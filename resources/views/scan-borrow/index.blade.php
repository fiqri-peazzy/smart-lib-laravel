@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">

    {{-- ============ STEP 1: SCANNER ============ --}}
    <div id="step-scanner">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pinjam Mandiri</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Scan QR Code buku untuk meminjam</p>
        </div>

        {{-- Tab switcher --}}
        <div class="flex rounded-xl bg-gray-100 dark:bg-gray-800 p-1 mb-4 gap-1">
            <button id="tab-camera" onclick="switchTab('camera')"
                class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow">
                <i class="bi bi-qr-code-scan mr-1"></i> Kamera
            </button>
            <button id="tab-upload" onclick="switchTab('upload')"
                class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500 dark:text-gray-400 hover:bg-white/60">
                <i class="bi bi-image mr-1"></i> Upload Gambar
            </button>
        </div>

        {{-- Camera panel --}}
        <div id="panel-camera">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative bg-black w-full h-[350px] sm:h-[400px] overflow-hidden flex justify-center items-center">
                    <div id="reader" class="w-full h-full"></div>

                    {{-- Idle --}}
                    <div id="state-idle" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/85 z-10">
                        <div class="w-20 h-20 rounded-full bg-blue-500/15 flex items-center justify-center mb-5 backdrop-blur-sm">
                            <i class="bi bi-qr-code text-5xl text-blue-400"></i>
                        </div>
                        <button onclick="startScanner()"
                            class="px-7 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-lg shadow-blue-600/25 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                            <i class="bi bi-camera-fill"></i> Buka Kamera
                        </button>
                    </div>
                </div>

                {{-- Controls --}}
                <div id="scanner-controls" class="hidden px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
                        Kamera aktif — arahkan ke QR
                    </span>
                    <button onclick="stopScanner()"
                        class="text-sm text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                        <i class="bi bi-stop-circle"></i> Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- Upload panel --}}
        <div id="panel-upload" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Upload foto / screenshot QR Code buku untuk verifikasi.
                </p>

                <label for="qr-upload"
                    class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-blue-300 dark:border-blue-700 rounded-xl cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    <i class="bi bi-cloud-upload text-4xl text-blue-400 mb-2"></i>
                    <span class="text-sm text-gray-500">Klik untuk pilih gambar QR Code</span>
                    <span class="text-xs text-gray-400 mt-1">PNG, JPG</span>
                    <input type="file" id="qr-upload" accept="image/*" class="hidden" onchange="testQRImage(this)">
                </label>

                <div id="upload-preview" class="mt-4 hidden">
                    <img id="upload-img" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 mb-3 max-h-56 object-contain bg-white">
                    <div id="upload-result" class="text-center py-4 rounded-lg bg-gray-50 dark:bg-gray-700 px-4">
                        <p class="text-sm text-gray-500 animate-pulse">Membaca QR Code...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 2: BOOK DETAIL ============ --}}
    <div id="step-detail" class="hidden">
        <div class="text-center mb-5">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Buku</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konfirmasi peminjaman buku</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="flex gap-4 p-5">
                <img id="book-cover" src="" alt="Cover"
                     class="w-24 h-32 rounded-lg object-cover shadow-md flex-shrink-0"
                     onerror="this.src='{{ asset('images/default-book-cover.png') }}'">
                <div class="flex-1 min-w-0">
                    <h2 id="book-title" class="text-lg font-bold text-gray-900 dark:text-white leading-tight"></h2>
                    <p id="book-author" class="text-sm text-gray-500 dark:text-gray-400 mt-1"></p>
                    <div class="flex gap-3 mt-3 flex-wrap">
                        <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg px-3 py-2 text-center min-w-[80px]">
                            <p class="text-xs text-blue-500 font-medium">Stok Tersedia</p>
                            <p id="book-stock" class="text-xl font-bold text-blue-700 dark:text-blue-300"></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2 text-center min-w-[80px]">
                            <p class="text-xs text-gray-500 font-medium">Lokasi</p>
                            <p id="book-rack" class="text-sm font-bold text-gray-700 dark:text-gray-200 mt-0.5"></p>
                        </div>
                        {{-- Slot info --}}
                        <div id="slot-info-box" class="bg-green-50 dark:bg-green-900/30 rounded-lg px-3 py-2 text-center min-w-[80px] hidden">
                            <p class="text-xs text-green-600 font-medium">Sisa Slot</p>
                            <p id="slot-info" class="text-xl font-bold text-green-700 dark:text-green-300"></p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="borrow-form" onsubmit="processBorrow(event)"
                  class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                @csrf
                <input type="hidden" id="book-barcode" name="barcode">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Jumlah Eksemplar
                    </label>
                    <p id="qty-hint" class="text-xs text-gray-400 dark:text-gray-500 mb-2"></p>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeQty(-1)"
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white font-bold text-xl flex items-center justify-center hover:bg-gray-200 transition-colors">−</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                               class="w-16 text-center text-xl font-bold border-0 bg-transparent text-gray-900 dark:text-white focus:outline-none">
                        <button type="button" onclick="changeQty(1)"
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white font-bold text-xl flex items-center justify-center hover:bg-gray-200 transition-colors">+</button>
                    </div>
                </div>

                {{-- Warning jika stok < yang diminta --}}
                <div id="stock-warning" class="hidden mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3 flex items-start gap-2 text-sm text-amber-700 dark:text-amber-300">
                    <i class="bi bi-exclamation-triangle-fill mt-0.5 flex-shrink-0"></i>
                    <span id="stock-warning-msg"></span>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="submit-btn-text">Konfirmasi Peminjaman</span>
                </button>

                <button type="button" onclick="backToScanner()"
                    class="w-full mt-2 py-2.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium flex items-center justify-center gap-1.5 transition-colors">
                    <i class="bi bi-arrow-left"></i> Scan Buku Lain
                </button>
            </form>
        </div>
    </div>

    {{-- ============ STEP 3: SUCCESS ============ --}}
    <div id="step-success" class="hidden text-center py-8">

        {{-- Icon: full success vs partial --}}
        <div id="success-icon-full" class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div id="success-icon-partial" class="hidden w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>

        <h2 id="success-title" class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Permintaan Terkirim!</h2>
        <p id="success-book-name" class="text-gray-700 dark:text-gray-300 font-medium mb-1"></p>
        <p id="success-subtitle" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>

        {{-- Partial info --}}
        <div id="success-partial-info" class="hidden mb-5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3 text-left">
            <p class="text-sm font-semibold text-amber-700 dark:text-amber-300 mb-1">
                <i class="bi bi-info-circle mr-1"></i>Diproses sebagian
            </p>
            <p id="success-partial-msg" class="text-sm text-amber-600 dark:text-amber-400"></p>
        </div>

        {{-- Assigned items list --}}
        <div id="success-items-wrap" class="hidden mb-5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-left">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
                <i class="bi bi-list-check mr-1"></i>Eksemplar yang di-assign
            </p>
            <ul id="success-items-list" class="space-y-1"></ul>
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Temui staf perpustakaan untuk pengambilan buku.</p>

        <div class="flex gap-3 justify-center flex-wrap">
            <a href="{{ route('dashboard') }}"
               class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                Lihat Peminjaman Saya
            </a>
            <button onclick="backToScanner()"
                class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Pinjam Lagi
            </button>
        </div>
    </div>

</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden max-w-sm w-full px-4">
    <div id="toast-inner" class="bg-red-600 text-white rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 text-sm font-medium">
        <i id="toast-icon" class="bi bi-exclamation-circle-fill text-lg"></i>
        <span id="toast-msg"></span>
    </div>
</div>

<style>
    #reader { background: #000; width: 100% !important; height: 100% !important; }
    #reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
    }
    #reader__scan_region { background: transparent !important; height: 100% !important; }
    #reader__dashboard { display: none !important; }
    #reader__scan_region img { display: none !important; }
    #reader__header_message { display: none !important; }
    #reader__scan_region video { border: none !important; }
    #qr-shaded-region { border: none !important; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let scanner    = null;
let scanLocked = false;
let maxQty     = 1; // diisi saat fetchBook berhasil

// ---- Helpers ----
function show(id) {
    ['step-scanner','step-detail','step-success'].forEach(s =>
        document.getElementById(s).classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
    window.scrollTo(0, 0);
}

// ---- Camera Scanner ----
function startScanner() {
    document.getElementById('state-idle').classList.add('hidden');
    scanLocked = false;

    if (!scanner) {
        scanner = new Html5Qrcode('reader', { verbose: false });
    }

    scanner.start(
        { facingMode: 'environment' },
        { fps: 15, disableFlip: false },
        onScanSuccess,
        () => {}
    ).then(() => {
        document.getElementById('scanner-controls').classList.remove('hidden');
    }).catch(err => {
        document.getElementById('state-idle').classList.remove('hidden');
        showToast('Gagal akses kamera: ' + err);
    });
}

function stopScanner() {
    if (scanner && scanner.isScanning) {
        scanner.stop().then(() => scanner.clear()).catch(() => {});
    }
    document.getElementById('scanner-controls').classList.add('hidden');
    document.getElementById('state-idle').classList.remove('hidden');
}

function onScanSuccess(decodedText) {
    if (scanLocked) return;
    scanLocked = true;

    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 1800; gain.gain.value = 0.15;
        osc.start(); osc.stop(ctx.currentTime + 0.12);
    } catch(e) {}

    fetchBook(decodedText);
}

// ---- Fetch Book ----
function fetchBook(barcode) {
    fetch('{{ route("scan-borrow.details") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ barcode })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            stopScanner();
            fillBookDetail(data.book);
            show('step-detail');
        } else {
            showToast(data.message || 'Buku tidak ditemukan.');
            setTimeout(() => { scanLocked = false; }, 2000);
        }
    })
    .catch(() => {
        showToast('Koneksi gagal.');
        setTimeout(() => { scanLocked = false; }, 1500);
    });
}

function fillBookDetail(book) {
    document.getElementById('book-cover').src          = book.cover_url || '';
    document.getElementById('book-title').textContent  = book.title;
    document.getElementById('book-author').textContent = book.author;
    document.getElementById('book-stock').textContent  = book.available_stock;
    document.getElementById('book-rack').textContent   = book.rack_location || '-';
    document.getElementById('book-barcode').value      = book.barcode;

    // max qty = stok tersedia (slot user dicek di backend, tapi kita tampilkan info)
    maxQty = book.available_stock || 1;

    const qtyInput = document.getElementById('quantity');
    qtyInput.max   = maxQty;
    qtyInput.value = Math.min(1, maxQty);

    // Hint teks
    document.getElementById('qty-hint').textContent =
        `Maksimal ${maxQty} eksemplar tersedia untuk judul ini.`;

    // Reset warning
    document.getElementById('stock-warning').classList.add('hidden');
    updateSubmitLabel();
}

// ---- Qty controls ----
function changeQty(d) {
    const input = document.getElementById('quantity');
    const newVal = Math.min(maxQty, Math.max(1, (parseInt(input.value) || 1) + d));
    input.value = newVal;
    onQtyChange();
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('quantity').addEventListener('input', onQtyChange);
});

function onQtyChange() {
    const input   = document.getElementById('quantity');
    let val       = parseInt(input.value) || 1;
    val           = Math.max(1, Math.min(maxQty, val));
    input.value   = val;

    const warn    = document.getElementById('stock-warning');
    const warnMsg = document.getElementById('stock-warning-msg');

    if (val >= maxQty && maxQty > 0) {
        warn.classList.remove('hidden');
        warnMsg.textContent = `Stok tersedia hanya ${maxQty} eksemplar. Sistem akan memproses sebanyak yang tersedia.`;
    } else {
        warn.classList.add('hidden');
    }

    updateSubmitLabel();
}

function updateSubmitLabel() {
    const val = parseInt(document.getElementById('quantity').value) || 1;
    document.getElementById('submit-btn-text').textContent =
        val > 1 ? `Konfirmasi Peminjaman (${val} Eksemplar)` : 'Konfirmasi Peminjaman';
}

// ---- Process Borrow ----
function processBorrow(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
    </svg> Memproses...`;

    const fd = new FormData(e.target);
    fetch('{{ route("scan-borrow.process") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(Object.fromEntries(fd))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccessStep(data);
        } else {
            showToast(data.message || 'Gagal memproses.');
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg><span id="submit-btn-text">Konfirmasi Peminjaman</span>`;
        }
    })
    .catch(() => {
        showToast('Koneksi gagal.');
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg><span id="submit-btn-text">Konfirmasi Peminjaman</span>`;
    });
}

// ---- Show Success Step ----
function showSuccessStep(data) {
    const isPartial = data.skipped > 0;

    // Icon
    document.getElementById('success-icon-full').classList.toggle('hidden', isPartial);
    document.getElementById('success-icon-partial').classList.toggle('hidden', !isPartial);

    // Title & subtitle
    document.getElementById('success-title').textContent = isPartial
        ? 'Diproses Sebagian'
        : 'Permintaan Terkirim!';

    document.getElementById('success-book-name').textContent =
        `${data.processed} eksemplar "${data.book?.title || ''}"`;

    document.getElementById('success-subtitle').textContent = isPartial
        ? `${data.requested} diminta, ${data.processed} berhasil diproses.`
        : 'Temui staf perpustakaan untuk pengambilan buku.';

    // Partial warning box
    const partialBox = document.getElementById('success-partial-info');
    if (isPartial) {
        partialBox.classList.remove('hidden');
        document.getElementById('success-partial-msg').textContent = data.message;
    } else {
        partialBox.classList.add('hidden');
    }

    // Assigned items list
    const itemsWrap = document.getElementById('success-items-wrap');
    const itemsList = document.getElementById('success-items-list');
    if (data.assigned_items && data.assigned_items.length > 0) {
        itemsList.innerHTML = data.assigned_items.map((qr, i) =>
            `<li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <span class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 text-xs flex items-center justify-center font-bold flex-shrink-0">${i+1}</span>
                <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">${qr}</span>
            </li>`
        ).join('');
        itemsWrap.classList.remove('hidden');
    } else {
        itemsWrap.classList.add('hidden');
    }

    show('step-success');
}

function backToScanner() {
    scanLocked = false;
    show('step-scanner');
    switchTab('camera');
    setTimeout(startScanner, 300);
}

// ---- Tab Switcher ----
function switchTab(tab) {
    const on  = 'flex-1 py-2 rounded-lg text-sm font-semibold transition-all bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow';
    const off = 'flex-1 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500 dark:text-gray-400 hover:bg-white/60';

    if (tab === 'upload') {
        stopScanner();
        document.getElementById('panel-camera').classList.add('hidden');
        document.getElementById('panel-upload').classList.remove('hidden');
        document.getElementById('tab-upload').className = on;
        document.getElementById('tab-camera').className = off;
    } else {
        document.getElementById('panel-upload').classList.add('hidden');
        document.getElementById('panel-camera').classList.remove('hidden');
        document.getElementById('tab-camera').className = on;
        document.getElementById('tab-upload').className = off;
    }
}

// ---- Upload & Decode QR Image ----
function testQRImage(input) {
    const file = input.files[0];
    if (!file) return;

    const url = URL.createObjectURL(file);
    document.getElementById('upload-img').src = url;
    document.getElementById('upload-preview').classList.remove('hidden');
    const resultBox = document.getElementById('upload-result');
    resultBox.innerHTML = '<p class="text-sm text-gray-500 animate-pulse">Membaca QR Code...</p>';

    const html5QrCode = new Html5Qrcode('upload-temp-reader', { verbose: false });

    html5QrCode.scanFile(file, true)
        .then(decodedText => {
            resultBox.innerHTML = '<p class="text-sm text-gray-500 animate-pulse">Memverifikasi ke database...</p>';

            fetch('{{ route("scan-borrow.details") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ barcode: decodedText })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    fillBookDetail(data.book);
                    show('step-detail');
                } else {
                    resultBox.innerHTML = `
                        <div class="text-amber-600 dark:text-amber-400">
                            <i class="bi bi-exclamation-triangle-fill text-3xl"></i>
                            <p class="font-bold text-base mt-2">Bukan QR Buku Perpustakaan</p>
                            <p class="text-xs text-gray-500 mt-1 bg-gray-100 dark:bg-gray-800 p-2 rounded">${decodedText}</p>
                        </div>`;
                    showToast(data.message || 'QR tidak dikenali.');
                }
            })
            .catch(() => {
                showToast('Koneksi server gagal.');
                resultBox.innerHTML = '<p class="text-red-500 font-semibold mt-2">Gagal terhubung ke server</p>';
            });

            URL.revokeObjectURL(url);
        })
        .catch(() => {
            resultBox.innerHTML = `
                <div class="text-red-500">
                    <i class="bi bi-x-circle-fill text-3xl"></i>
                    <p class="font-semibold mt-2">QR Code tidak terbaca</p>
                    <p class="text-xs text-gray-500 mt-2">Pastikan gambar jelas, QR tidak terpotong, pencahayaan cukup.</p>
                </div>`;
            URL.revokeObjectURL(url);
        });

    input.value = '';
}

// ---- Toast ----
function showToast(msg, type = 'error') {
    const t    = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon  = document.getElementById('toast-icon');
    document.getElementById('toast-msg').textContent = msg;

    if (type === 'success') {
        inner.className = 'bg-green-600 text-white rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 text-sm font-medium';
        icon.className  = 'bi bi-check-circle-fill text-lg';
    } else if (type === 'warning') {
        inner.className = 'bg-amber-500 text-white rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 text-sm font-medium';
        icon.className  = 'bi bi-exclamation-triangle-fill text-lg';
    } else {
        inner.className = 'bg-red-600 text-white rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 text-sm font-medium';
        icon.className  = 'bi bi-exclamation-circle-fill text-lg';
    }

    t.classList.remove('hidden');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.add('hidden'), 3500);
}
</script>
<div id="upload-temp-reader" style="display:none;"></div>
@endpush