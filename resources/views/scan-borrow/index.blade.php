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
                            <p class="text-xs text-blue-500 font-medium">Stok</p>
                            <p id="book-stock" class="text-xl font-bold text-blue-700 dark:text-blue-300"></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2 text-center min-w-[80px]">
                            <p class="text-xs text-gray-500 font-medium">Lokasi</p>
                            <p id="book-rack" class="text-sm font-bold text-gray-700 dark:text-gray-200 mt-0.5"></p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="borrow-form" onsubmit="processBorrow(event)"
                  class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700 pt-4">
                @csrf
                <input type="hidden" id="book-barcode" name="barcode">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Eksemplar</label>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeQty(-1)"
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white font-bold text-xl flex items-center justify-center hover:bg-gray-200 transition-colors">−</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                               class="w-16 text-center text-xl font-bold border-0 bg-transparent text-gray-900 dark:text-white focus:outline-none">
                        <button type="button" onclick="changeQty(1)"
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-white font-bold text-xl flex items-center justify-center hover:bg-gray-200 transition-colors">+</button>
                    </div>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Konfirmasi Peminjaman
                </button>

                <button type="button" onclick="backToScanner()"
                    class="w-full mt-2 py-2.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium flex items-center justify-center gap-1.5 transition-colors">
                    <i class="bi bi-arrow-left"></i> Scan Buku Lain
                </button>
            </form>
        </div>
    </div>

    {{-- ============ STEP 3: SUCCESS ============ --}}
    <div id="step-success" class="hidden text-center py-10">
        <div class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Permintaan Terkirim!</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-1" id="success-book-name"></p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Temui staf perpustakaan untuk pengambilan buku.</p>
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
        <i class="bi bi-exclamation-circle-fill text-lg"></i>
        <span id="toast-msg"></span>
    </div>
</div>

<style>
    /* html5-qrcode styles */
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
    /* Remove built-in border */
    #reader__scan_region video { border: none !important; }
    #qr-shaded-region { border: none !important; }
</style>
@endsection

@push('scripts')
{{-- html5-qrcode: QR scanner library dari Google, sangat stabil untuk smartphone --}}
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let scanner = null;
let scanLocked = false;

// ---- Helpers ----
function show(id) {
    ['step-scanner','step-detail','step-success'].forEach(s =>
        document.getElementById(s).classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
    window.scrollTo(0, 0);
}

// ---- Camera Scanner (html5-qrcode) ----
function startScanner() {
    document.getElementById('state-idle').classList.add('hidden');
    scanLocked = false;

    if (!scanner) {
        scanner = new Html5Qrcode('reader', { verbose: false });
    }

    scanner.start(
        { facingMode: 'environment' },
        {
            fps: 15,
            disableFlip: false
        },
        onScanSuccess,
        () => {} // error tiap frame kosong = normal, abaikan
    ).then(() => {
        document.getElementById('scanner-controls').classList.remove('hidden');
    }).catch(err => {
        document.getElementById('state-idle').classList.remove('hidden');
        showToast('Gagal akses kamera: ' + err);
        console.error(err);
    });
}

function stopScanner() {
    if (scanner && scanner.isScanning) {
        scanner.stop().then(() => {
            scanner.clear();
        }).catch(() => {});
    }
    document.getElementById('scanner-controls').classList.add('hidden');
    document.getElementById('state-idle').classList.remove('hidden');
}

function onScanSuccess(decodedText) {
    if (scanLocked) return;
    scanLocked = true;
    console.log('QR terbaca:', decodedText);

    // Beep feedback
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.value = 1800;
        gain.gain.value = 0.15;
        osc.start();
        osc.stop(audioCtx.currentTime + 0.12);
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
            // Ditemukan → stop scanner & tampil detail
            stopScanner();
            fillBookDetail(data.book);
            show('step-detail');
        } else {
            // Tidak ditemukan — lanjut scan
            console.log('Buku tidak ditemukan:', barcode);
            showToast('QR tidak memuat data ID buku apa pun.');
            setTimeout(() => { scanLocked = false; }, 2000);
        }
    })
    .catch(() => {
        showToast('Koneksi gagal.');
        setTimeout(() => { scanLocked = false; }, 1500);
    });
}

function fillBookDetail(book) {
    document.getElementById('book-cover').src   = book.cover_url || '';
    document.getElementById('book-title').textContent  = book.title;
    document.getElementById('book-author').textContent = book.author;
    document.getElementById('book-stock').textContent  = book.available_stock;
    document.getElementById('book-rack').textContent   = book.rack_location || '-';
    document.getElementById('book-barcode').value      = book.barcode;
    document.getElementById('quantity').max   = book.available_stock;
    document.getElementById('quantity').value = 1;
}

function changeQty(d) {
    const i = document.getElementById('quantity');
    i.value = Math.min(parseInt(i.max)||99, Math.max(1, (parseInt(i.value)||1) + d));
}

function processBorrow(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...';
    const fd = new FormData(e.target);
    fetch('{{ route("scan-borrow.process") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(Object.fromEntries(fd))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('success-book-name').textContent = data.book?.title || '';
            show('step-success');
        } else {
            showToast(data.message || 'Gagal memproses.');
            btn.disabled = false;
            btn.innerHTML = 'Konfirmasi Peminjaman';
        }
    })
    .catch(() => {
        showToast('Koneksi gagal.');
        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi Peminjaman';
    });
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
        document.getElementById('tab-upload').className  = on;
        document.getElementById('tab-camera').className  = off;
    } else {
        document.getElementById('panel-upload').classList.add('hidden');
        document.getElementById('panel-camera').classList.remove('hidden');
        document.getElementById('tab-camera').className  = on;
        document.getElementById('tab-upload').className  = off;
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
                    // Valid book
                    fillBookDetail(data.book);
                    show('step-detail');
                } else {
                    // Invalid QR / not a book
                    resultBox.innerHTML = `
                        <div class="text-amber-600 dark:text-amber-400">
                            <i class="bi bi-exclamation-triangle-fill text-3xl"></i>
                            <p class="font-bold text-base mt-2">Bukan QR Buku Perpustakaan</p>
                            <p class="text-xs text-gray-500 mt-1 bg-gray-100 dark:bg-gray-800 p-2 rounded overflow-hidden text-ellipsis">${decodedText}</p>
                        </div>`;
                    showToast('QR tidak memuat data ID buku apa pun.');
                }
            })
            .catch(() => {
                showToast('Koneksi server gagal.');
                resultBox.innerHTML = '<p class="text-red-500 font-semibold mt-2">Gagal terhubung ke server</p>';
            });

            URL.revokeObjectURL(url);
        })
        .catch(err => {
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
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-inner').className =
        (type === 'success' ? 'bg-green-600' : 'bg-red-600') +
        ' text-white rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 text-sm font-medium';
    t.classList.remove('hidden');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.add('hidden'), 3500);
}
</script>
{{-- Hidden div for scanFile utility --}}
<div id="upload-temp-reader" style="display:none;"></div>
@endpush
