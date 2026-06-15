<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat Datang di IKRA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #eef2f3;
            padding-bottom: 20px;
        }
        .header h2 {
            color: #2c3e50;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            line-height: 1.6;
            color: #4a5568;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #eef2f3;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>IKRA - Portal Anggota</h2>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        
        <p>Selamat! Pendaftaran kamu di sistem <strong>SI-Infak-Ikra</strong> telah berhasil dilakukan. Kami sangat senang kamu bisa bergabung bersama keluarga besar Ikatan Keluarga Remaja Masjid.</p>
        
        <p>Sekarang kamu sudah bisa login ke dashboard menggunakan akun email yang kamu daftarkan (<strong>{{ $user->email }}</strong>) untuk memantau status infak bulanan atau berkontribusi dalam program infak ekstra.</p>
        
        <p>Semoga aplikasi ini dapat mempermudah kita semua dalam beramal dan menebar kebaikan.</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim otomatis oleh Sistem Informasi Infak IKRA.<br>&copy; {{ date('Y') }} IKRA. All rights reserved.</p>
    </div>
</div>

</body>
</html>