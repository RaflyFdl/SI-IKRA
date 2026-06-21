<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Ditangguhkan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #dc3545;">Mohon Maaf, Pendaftaran Anda Belum Disetujui</h2>
        <p>Halo, <strong>{{ $namaPendaftar }}</strong>,</p>
        <p>Terima kasih telah melakukan pendaftaran di Sistem Informasi Infak IKRA. Setelah dilakukan verifikasi berkas oleh pihak admin, dengan berat hati kami menginformasikan bahwa pendaftaran Anda saat ini **belum dapat disetujui**.</p>
        
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-left: 5px solid #dc3545; margin: 20px 0; border-radius: 4px;">
            <strong>Alasan Penolakan:</strong><br>
            <p style="margin: 5px 0 0 0; white-space: pre-line;">{{ $alasan }}</p>
        </div>

        <p>Silakan lakukan pendaftaran ulang atau hubungi sekretariat yayasan jika ada kekeliruan data/berkas pendukung.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('register') }}" style="background-color: #dc3545; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Daftar Ulang Kembali</a>
        </div>

        <p style="font-size: 0.9em; color: #777;">Wassalamu'alaikum Warahmatullahi Wabarakatuh,<br><strong>Sistem Informasi Infak IKRA</strong></p>
    </div>
</body>
</html>