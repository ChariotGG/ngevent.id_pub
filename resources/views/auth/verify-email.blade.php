<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - ngevent.id</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            padding: 48px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(255, 143, 199, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo h1 {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #FF8FC7 0%, #ff6fb8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 24px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #000;
            margin-bottom: 16px;
            text-align: center;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .email-display {
            background: #f9fafb;
            padding: 12px 16px;
            border-radius: 8px;
            text-align: center;
            margin: 24px 0;
            font-weight: 600;
            color: #000;
        }

        .alert {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: center;
        }

        .resend-section {
            background: #fff5f8;
            border-left: 4px solid #FF8FC7;
            padding: 16px;
            border-radius: 8px;
            margin-top: 24px;
        }

        .resend-section p {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .btn {
            background: linear-gradient(135deg, #FF8FC7 0%, #ff6fb8 100%);
            color: #000;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .steps {
            margin: 24px 0;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .step-number {
            background: linear-gradient(135deg, #FF8FC7 0%, #ff6fb8 100%);
            color: #000;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-content strong {
            display: block;
            margin-bottom: 4px;
            color: #000;
        }

        .step-content span {
            font-size: 14px;
            color: #666;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            font-size: 13px;
            color: #999;
        }

        .footer a {
            color: #FF8FC7;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>ngevent.id</h1>
            <p>Platform Ticketing Event Terpercaya</p>
        </div>

        <div class="icon">📧</div>

        <h2>Verifikasi Email Kamu</h2>

        @if (session('success'))
            <div class="alert">
                ✓ {{ session('success') }}
            </div>
        @endif

        <p style="text-align: center;">
            Terima kasih telah mendaftar sebagai <strong>Organizer</strong> di ngevent.id!
        </p>

        <div class="email-display">
            {{ auth()->user()->email }}
        </div>

        <p style="text-align: center; color: #333;">
            Kami telah mengirim link verifikasi ke email kamu. Mohon cek inbox atau folder spam.
        </p>

        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <strong>Buka Email Kamu</strong>
                    <span>Cek inbox atau folder spam</span>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <strong>Klik Link Verifikasi</strong>
                    <span>Link akan kedaluwarsa dalam 60 menit</span>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <strong>Mulai Buat Event!</strong>
                    <span>Akses penuh ke dashboard organizer</span>
                </div>
            </div>
        </div>

        <div class="resend-section">
            <p><strong>Tidak menerima email?</strong></p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn" id="resendBtn">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
        </div>

        <div class="footer">
            <p>Butuh bantuan? Hubungi kami di <a href="mailto:support@ngevent.id">support@ngevent.id</a></p>
        </div>
    </div>

    <script>
        // Disable button after click untuk prevent spam
        document.getElementById('resendBtn')?.addEventListener('click', function() {
            this.disabled = true;
            this.textContent = 'Mengirim...';
        });
    </script>
</body>
</html>
