{{-- resources/views/layouts/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Pocket Library' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1f2937;
            line-height: 1.5;
        }

        .container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 20mm auto;
            background: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-radius: 16px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .logo svg {
            width: 50px;
            height: 50px;
            color: #4f46e5;
        }

        h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        h1 span {
            font-size: 18px;
            opacity: 0.9;
            display: block;
            margin-top: 8px;
            font-weight: 500;
        }

        .meta {
            background: white;
            color: #4b5563;
            padding: 20px 40px;
            text-align: center;
            font-size: 14px;
            border-bottom: 3px solid #e5e7eb;
        }

        .content {
            padding: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        th {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 16px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        tr:hover {
            background: #f8fafc;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        .badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .footer {
            text-align: center;
            padding: 30px 40px;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            margin-top: 40px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(79, 70, 229, 0.05);
            font-weight: 900;
            pointer-events: none;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="watermark">POCKET LIBRARY</div>
    
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 19h16v-2H4v2zm16-6H4v2h16v-2zm0-4H4v2h16V9z"/>
                </svg>
            </div>
            <h1>
                {{ $title ?? 'Laporan Pocket Library' }}
                @if(isset($subtitle))
                    <span>{{ $subtitle }}</span>
                @endif
            </h1>
        </div>

        <div class="meta">
            <strong>Dicetak pada:</strong> {{ now()->format('d F Y, H:i') }} WIB<br>
            <strong>Oleh:</strong> {{ auth()->user()->nama_lengkap ?? auth()->user()->username }}
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Pocket Library - Sistem Perpustakaan Digital</p>
            <p>Generated by Pocket Library v1.0</p>
        </div>
    </div>
</body>
</html>