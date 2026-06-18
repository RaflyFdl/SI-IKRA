<!DOCTYPE html>
<html>
<head>
    <title>Selamat Datang di IKRA Padjadjaran</title>
</head>
<body style="background-color: #f8fafc; font-family: sans-serif; color: #334155; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #0b6e3f; color: #ffffff; padding: 30px; text-align: center;">
            <h2 style="margin: 0; font-size: 24px;">🎉 Pendaftaran Disetujui!</h2>
        </div>
        <div style="padding: 30px; line-height: 1.6;">
            <p>Assalamualaikum <strong>{{ $nama }}</strong>,</p>
            <p>Alhamdulillah, Admin telah memverifikasi data Anda dan menyatakan bahwa Anda resmi terdaftar menjadi bagian dari <strong>Anggota Yayasan IKRA Padjadjaran</strong>.</p>
            
            <div style="background-color: #f1f5f9; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px;">
                <strong>Status Akun:</strong> <span style="color: #16a34a; font-weight: bold;">AKTIF</span><br>
                <strong>Fasilitas:</strong> Anda sekarang sudah bisa masuk ke dashboard anggota untuk berkontribusi dalam melakukan infak dan menebar kebaikan seluas-luasnya.
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" style="background-color: #0b6e3f; color: #ffffff; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; display: inline-block;">
                    Masuk ke Portal Anggota
                </a>
            </div>
            
            <p>Selamat bergabung! Semoga kontribusi infak Anda menjadi ladang pahala jariah yang terus mengalir.</p>
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <p style="font-size: 13px; text-align: center; color: #94a3b8; margin: 0;">Salam hangat, <br><strong>Pengurus Yayasan Wakaf IKRA Padjadjaran</strong></p>
        </div>
    </div>
</body>
</html>