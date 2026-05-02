@php
    $qrcodeUrl = route('books.qrcode', $getRecord());
    $printUrl = route('books.qrcode.print', $getRecord());
    $barcode = $getState();
@endphp

<div class="flex flex-col sm:flex-row items-start gap-4">
    {{-- QR Code Display Card --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200" style="width: 200px;">
        <div class="flex justify-center">
            <img src="{{ $qrcodeUrl }}"
                 alt="QR Code {{ $barcode }}"
                 style="width: 160px; height: 160px; image-rendering: pixelated;">
        </div>
        <div class="text-center mt-2 text-black font-mono font-bold tracking-wide" style="font-size: 10px;">
            {{ $barcode }}
        </div>
        <div class="text-center text-gray-400 mt-1" style="font-size: 10px;">Label QR buku</div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col gap-2 mt-1">
        <button type="button"
                onclick="window.open('{{ $printUrl }}', '_blank')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
                style="background-color: #4f46e5;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Label
        </button>

        <a href="{{ $qrcodeUrl }}" download="qrcode-{{ $barcode }}.png"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors"
           style="background-color: #16a34a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Unduh PNG
        </a>
    </div>
</div>
