<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Infak Ekstra Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #28a745;">Alhamdulillah, Infak Ekstra Berhasil Diterima!</h2>
        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
        <p>Terima kasih atas kebaikan dan partisipasi aktif Anda. Kami mengonfirmasi bahwa pembayaran untuk **Infak Program Ekstra** Anda telah sukses diproses:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold; width: 35%;">Nama Program Ekstra</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: {{ $namaProgram }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Nominal Infak</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; color: #28a745; font-weight: bold;">: Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">Waktu Transaksi</td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: {{ now()->format('d F Y H:i') }} WIB</td>
            </tr>
        </table>

        <p><i>"Semoga Allah SWT melipatgandakan pahala atas infak ekstra yang Anda salurkan, menjadikannya pembersih harta, serta mengalirkan keberkahan yang tiada putus untuk Anda dan keluarga. Aamiin."</i></p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777;">Email ini dikirimkan secara otomatis oleh Sistem Manajemen Infak Ikra. Harap tidak membalas pesan ini.</p>
    </div>
</body>
</html>