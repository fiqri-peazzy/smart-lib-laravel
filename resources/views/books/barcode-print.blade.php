<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barcode - {{ $book->title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            max-width: 400px;
            width: 100%;
        }
        .library-name {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 16px;
        }
        .barcode-img {
            width: 100%;
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .barcode-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #1e293b;
            margin-top: 10px;
        }
        .book-title {
            font-size: 13px;
            color: #475569;
            margin-top: 8px;
            font-weight: 500;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .divider {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 16px 0;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 28px;
        }
        .btn {
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-success { background: #16a34a; color: white; }
        .btn-success:hover { background: #15803d; }

        @media print {
            body { background: white; padding: 0; }
            .actions { display: none; }
            .card { box-shadow: none; border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="library-name">FIKOM Smart Library</div>
        <img src="{{ route('books.barcode', $book) }}"
             alt="Barcode {{ $book->barcode }}"
             class="barcode-img">
        <div class="barcode-code">{{ $book->barcode }}</div>
        <hr class="divider">
        <div class="book-title">{{ $book->title }}</div>
    </div>

    <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Print Label
        </button>
        <a href="{{ route('books.barcode', $book) }}" download="barcode-{{ $book->barcode }}.png"
           class="btn btn-success">
            ⬇️ Unduh PNG
        </a>
    </div>
</body>
</html>
