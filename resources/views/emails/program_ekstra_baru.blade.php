<!DOCTYPE html>
<html>
<head>
    <title>Program Infak Ekstra Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #0056b3;">📢 Program Infak Ekstra Baru Telah Dibuka!</h2>
        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
        <p>Halo Sahabat Anggota IKRA, ada ladang kebaikan baru yang bisa kita tanam bersama. Admin Yayasan baru saja membuka program infak ekstra berikut:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; width: 30%;">Nama Program</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $program->name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Kategori</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;"><span style="background-color: #e2f0fe; color: #0056b3; padding: 3px 8px; border-radius: 3px; font-size: 0.9em;">{{ $program->category }}</span></td>
            </tr>
            @if(isset($program->target_amount) && $program->target_amount > 0)
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Target Dana</td>
                <td style="padding: 10px; border: 1px solid #dee2e6; color: #dc3545; font-weight: bold;">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">Deskripsi</td>
                <td style="padding: 10px; border: 1px solid #dee2e6; white-space: pre-line;">{{ $program->description }}</td>
            </tr>
        </table>

        <p>Mari ikut berkontribusi dan luaskan syiar kemanfaatan yayasan melalui partisipasi aktif Anda pada program infak ekstra ini.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('login') }}" style="background-color: #0056b3; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ikut Berinfak Sekarang</a>
        </div>

        <p style="font-size: 0.9em; color: #777;">Jazakumullah khairan katsiran atas perhatian dan ketulusan Anda.<br><strong>Sistem Informasi Infak IKRA</strong></p>
    </div>
</body>
</html>