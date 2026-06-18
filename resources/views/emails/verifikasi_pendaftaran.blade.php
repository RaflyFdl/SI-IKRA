<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email Pendaftaran</title>
</head>
<body style="background-color: #f8fafc; font-family: sans-serif; color: #334155; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #0b6e3f; color: #ffffff; padding: 30px; text-align: center;">
            <h2 style="margin: 0; font-size: 24px;">Halo Calon Anggota IKRA!</h2>
        </div>
        <div style="padding: 30px; line-height: 1.6;">
            <p>Terima kasih telah mengisi form pendaftaran di <strong>Portal IKRA Padjadjaran</strong>.</p>
            <p>Untuk memastikan bahwa ini benar-benar email Anda, silakan lakukan verifikasi terlebih dahulu dengan mengklik tombol di bawah ini:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $linkVerifikasi }}" style="background-color: #0b6e3f; color: #ffffff; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; display: inline-block;">
                    Verifikasi Akun Saya
                </a>
            </div>
            
            <p style="font-size: 12px; color: #64748b;">Jika Anda merasa tidak melakukan pendaftaran ini, silakan abaikan email ini.</p>
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <p style="font-size: 13px; text-align: center; color: #94a3b8; margin: 0;">Sistem Keuangan Otomatis - Yayasan IKRA</p>
        </div>
    </div>
</body>
</html>