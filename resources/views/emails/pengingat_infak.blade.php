<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Infak Reguler</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #1c7430;">Periode Baru Infak Reguler Telah Tiba!</h2>
        <p>Halo, <strong>{{ $namaAnggota }}</strong>,</p>
        <p>Kami ingin menginformasikan bahwa periode infak reguler untuk bulan <strong>{{ $bulanTahun }}</strong> sudah dimulai.</p>
        <p>Mari salurkan infak reguler bulanan Anda untuk mendukung keberlangsungan program kemanusiaan dan operasional yayasan kita melalui nomor Virtual Account (VA) Muamalat yang tertera pada dashboard profil Anda.</p>
        
        <<div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('login') }}" style="background-color: #1c7430; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Masuk ke Dashboard</a>
        </div>
        <p style="font-size: 0.9em; color: #777;">Jazakumullah khairan katsiran atas konsistensi dan kedermawanan Anda.<br><strong>Sistem Informasi Infak IKRA</strong></p>
    </div>
</body>
</html>