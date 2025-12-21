<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $item->barcode }}</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            @page {
                margin: 0;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .qr-container {
            background: white;
            border: 3px solid #333;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }

        .qr-container img {
            width: 250px;
            height: 250px;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }

        .barcode {
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            color: #333;
        }

        .book-title {
            font-size: 16px;
            margin-top: 10px;
            color: #666;
            line-height: 1.4;
        }

        .book-info {
            font-size: 14px;
            color: #999;
            margin-top: 8px;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #6366f1;
            color: white;
        }

        .btn-primary:hover {
            background: #4f46e5;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }
    </style>
</head>

<body>
    <div>
        <div class="qr-container">
            @if ($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code">
            @else
                <div
                    style="width: 250px; height: 250px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                    <p style="color: #999;">QR Code not generated</p>
                </div>
            @endif

            <div class="barcode">{{ $item->barcode }}</div>
            <div class="book-title">{{ $item->book->title }}</div>
            <div class="book-info">{{ $item->book->author }}</div>
        </div>

        <div class="actions no-print">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Print
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                ✖️ Close
            </button>
        </div>
    </div>
</body>

</html>
