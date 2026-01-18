<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - ngevent.id</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #000000;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #000000; padding: 20px 0;">
        <tr>
            <td align="center">

                <!-- Container -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(255, 143, 199, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #FF8FC7 0%, #ff6fb8 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 36px; font-weight: 900; color: #000000; text-transform: lowercase;">ngevent.id</h1>
                            <p style="margin: 8px 0 0 0; font-size: 13px; color: #000000; opacity: 0.8; font-weight: 600;">Platform Ticketing Event Terpercaya</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">

                            <h2 style="margin: 0 0 16px 0; font-size: 24px; font-weight: 700; color: #000000;">Halo, {{ $user->name }}! 👋</h2>

                            <p style="margin: 0 0 16px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Terima kasih sudah mendaftar sebagai <strong>Organizer</strong> di ngevent.id! Kami sangat senang kamu bergabung dengan kami.
                            </p>

                            <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Untuk mulai membuat dan mengelola event kamu, silakan verifikasi alamat email dengan mengklik tombol di bawah ini:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 16px 0;">
                                        <a href="{{ $url }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #FF8FC7 0%, #ff6fb8 100%); color: #000000; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 12px rgba(255, 143, 199, 0.3);">
                                            ✓ Verifikasi Email Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
                                <tr>
                                    <td style="background-color: #fff5f8; border-left: 4px solid #FF8FC7; padding: 16px; border-radius: 4px;">
                                        <p style="margin: 0; font-size: 14px; color: #666666;">
                                            🔒 <strong>Link verifikasi ini akan kedaluwarsa dalam 60 menit</strong> demi keamanan akun kamu.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <div style="height: 1px; background: linear-gradient(90deg, transparent, #e5e7eb, transparent); margin: 24px 0;"></div>

                            <!-- Fallback URL -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color: #f9fafb; padding: 16px; border-radius: 8px;">
                                        <p style="margin: 0 0 8px 0; font-size: 14px; color: #666666;"><strong>Tombol tidak berfungsi?</strong></p>
                                        <p style="margin: 0 0 12px 0; font-size: 14px; color: #666666;">Salin dan paste link berikut ke browser kamu:</p>
                                        <p style="margin: 0; word-break: break-all;">
                                            <a href="{{ $url }}" style="color: #FF8FC7; font-size: 13px; text-decoration: none;">{{ $url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <div style="height: 1px; background: linear-gradient(90deg, transparent, #e5e7eb, transparent); margin: 24px 0;"></div>

                            <!-- Additional Info -->
                            <p style="margin: 0 0 16px 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                <strong>Kenapa saya menerima email ini?</strong><br>
                                Kamu menerima email ini karena alamat email ini didaftarkan sebagai Organizer di ngevent.id.
                                Jika kamu tidak merasa mendaftar, abaikan email ini.
                            </p>

                            <p style="margin: 0; font-size: 14px; color: #666666;">
                                Butuh bantuan? Hubungi kami di <a href="mailto:support@ngevent.id" style="color: #FF8FC7; text-decoration: none; font-weight: 600;">support@ngevent.id</a>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #000000; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 16px 0; color: #999999; font-size: 13px; line-height: 1.6;">
                                © {{ date('Y') }} ngevent.id. Semua hak dilindungi.<br>
                                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                            </p>
                            <p style="margin: 0;">
                                <a href="https://ngevent.id" style="color: #FF8FC7; text-decoration: none; margin: 0 12px; font-size: 13px;">Website</a>
                                <a href="https://ngevent.id/privacy" style="color: #FF8FC7; text-decoration: none; margin: 0 12px; font-size: 13px;">Kebijakan Privasi</a>
                                <a href="https://ngevent.id/terms" style="color: #FF8FC7; text-decoration: none; margin: 0 12px; font-size: 13px;">Syarat & Ketentuan</a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
