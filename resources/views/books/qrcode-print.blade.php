<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR Code - {{ $item->book->title }}</title>
    <style>
        @page { size: 60mm 60mm; margin: 3mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .qr-container {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .qr-img {
            width: 140px;
            height: 140px;
            image-rendering: pixelated;
        }
        .qr-code-text {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 4px;
            word-break: break-all;
        }
        .qr-label {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }
        .book-title {
            font-size: 10px;
            font-weight: bold;
            margin-top: 6px;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        @media print {
            body { height: auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <img src="{{ route('book-items.qrcode', $item) }}" alt="QR" class="qr-img">
        <div class="qr-code-text">{{ $item->qr_code }}</div>
        <div class="qr-label">Smart Library</div>
        <div class="book-title">{{ Str::limit($item->book->title, 30) }}</div>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 24px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 600;">
            🖨️ Cetak Label
        </button>
    </div>

    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
