<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Yayasan Wakaf IKRA Padjadjaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 text-slate-800 font-sans antialiased">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-[#0b6e3f] text-white p-8 text-center space-y-2">
            <div class="bg-white text-[#0b6e3f] font-extrabold text-2xl w-12 h-12 flex items-center justify-center rounded-xl mx-auto shadow-md">
                I
            </div>
            <h2 class="text-xl font-bold tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-xs text-emerald-100/80">Sistem Pengelolaan Infak Terpadu Alumni FK Unpad</p>
        </div>

        <div class="p-8 space-y-6">
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-medium p-4 rounded-xl flex items-center space-x-2">
                    <span>⚠️ {{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium p-4 rounded-xl flex items-center space-x-2">
                    <span>✅ {{ session('success') }}</span>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-4">
                @csrf <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Alamat Email</label>
                    <input type="email" name="email" id="email" required placeholder="Masukkan email Anda (contoh: keuangan@ikra.com)" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#0b6e3f] focus:bg-white transition-all">
                    @error('email')
                        <span class="text-xs text-red-600 block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-amber-50 border border-amber-200/60 p-3 rounded-xl text-[11px] text-amber-800 leading-relaxed">
                    💡 <strong>Tips Simulasi Skripsi:</strong> Sistem mendeteksi role secara otomatis berdasarkan email yang terdaftar di database (Admin, Keuangan, atau Anggota).
                </div>

                <button type="submit" 
                    class="w-full bg-[#0b6e3f] hover:bg-[#085430] text-white font-bold text-sm py-3.5 rounded-xl transition-all shadow-md tracking-wide mt-2 transform active:scale-[0.98]">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <div class="bg-slate-50 p-5 text-center border-t border-gray-100">
            <p class="text-xs text-gray-500">
                Belum punya akun Anggota? <a href="/daftar" class="text-[#0b6e3f] font-bold hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>

</body>
</html>