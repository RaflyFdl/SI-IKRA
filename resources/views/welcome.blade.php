<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yayasan Wakaf IKRA Padjadjaran - Bersama Berinfak, Bersama Memberi Manfaat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- 1. NAVBAR -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-[#0b6e3f] text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-sm">
                    I
                </div>
                <div>
                    <span class="font-bold text-slate-900 block text-sm leading-tight tracking-tight">Yayasan Wakaf IKRA</span>
                    <span class="text-[11px] text-slate-400 font-medium block">Padjadjaran</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/login" class="text-sm font-semibold text-slate-600 hover:text-[#0b6e3f] transition">Masuk</a>
                <a href="/daftar" class="bg-[#0b6e3f] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#095732] shadow-sm transition">Daftar</a>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION -->
    <section class="bg-gradient-to-br from-[#074729] to-[#0b6e3f] text-white py-20 px-6 relative overflow-hidden">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block bg-white/10 backdrop-blur-md text-emerald-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider border border-white/10">
                    Alumni FK Universitas Padjadjaran
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                    Bersama Berinfak,<br class="hidden md:inline"> Bersama Memberi Manfaat
                </h1>
                <p class="text-emerald-100/80 text-base md:text-lg font-normal max-w-2xl leading-relaxed">
                    Yayasan Wakaf IKRA Padjadjaran adalah wadah bagi alumni FK Unpad untuk bersama-sama mengelola dan menyalurkan infak secara transparan dan terstruktur.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="/daftar" class="bg-white text-[#0b6e3f] font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-slate-50 shadow-md transition">
                        Daftar Sekarang
                    </a>
                    <a href="/login" class="bg-transparent border-2 border-white text-white font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-white/10 transition">
                        Masuk
                    </a>
                </div>
            </div>
        </div>

        <!-- STATS BAR (REAL-TIME DATABASE) -->
        <div class="max-w-7xl mx-auto mt-16 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-5 rounded-2xl text-center">
                <h4 class="text-2xl md:text-3xl font-black tracking-tight">{{ $anggotaAktif }}</h4>
                <p class="text-xs text-emerald-200 font-medium mt-1">Anggota Aktif</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-5 rounded-2xl text-center">
                <h4 class="text-2xl md:text-3xl font-black tracking-tight">{{ $programAktif }}</h4>
                <p class="text-xs text-emerald-200 font-medium mt-1">Program Aktif</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-5 rounded-2xl text-center">
                <h4 class="text-2xl md:text-3xl font-black tracking-tight">{{ $totalDanaFormat }}</h4>
                <p class="text-xs text-emerald-200 font-medium mt-1">Total Dana Terkumpul</p>
            </div>
            <div class="bg-white/10 backdrop-blur-sm border border-white/10 p-5 rounded-2xl text-center">
                <h4 class="text-2xl md:text-3xl font-black tracking-tight">{{ $programTersalurkan }}</h4>
                <p class="text-xs text-emerald-200 font-medium mt-1">Program Tersalurkan</p>
            </div>
        </div>
    </section>

    <!-- 3. SEKSI KEGIATAN & PROGRAM TERLAKSANA (REFERENSI image_47b9c1.png) -->
    <section class="max-w-7xl mx-auto px-6 py-20 space-y-12">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Jejak Langkah & Kegiatan Yayasan</h2>
            <p class="text-sm text-slate-500 max-w-xl mx-auto">Realisasi nyata penyaluran amanah infak para alumni yang telah dirasakan manfaatnya secara langsung.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Kegiatan 1 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-xl text-emerald-600">
                    🍱
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Penyaluran Makanan Anak Yatim</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Pendistribusian paket makanan bergizi secara rutin setiap hari Jumat untuk panti asuhan binaan di sekitar wilayah Bandung Raya.
                </p>
            </div>

            <!-- Kegiatan 2 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-xl text-blue-600">
                    🌱
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Reboisasi & Penanaman 1000 Pohon</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Aksi kepedulian lingkungan bersama mahasiswa dan siswa sekolah dengan menanam bibit pohon pelindung guna mencegah abrasi tanah.
                </p>
            </div>

            <!-- Kegiatan 3 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-xl text-indigo-600">
                    🎓
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Beasiswa Pendidikan Yatim</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Pemberian bantuan dana pendidikan serta perlengkapan sekolah bagi anak-anak yatim berprestasi yang kurang mampu.
                </p>
            </div>

            <!-- Kegiatan 4 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-xl text-amber-600">
                    🩺
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Pengobatan Gratis IKRA</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Pelayanan pemeriksaan kesehatan dan pembagian obat gratis oleh Tim Dokter Alumni FK Unpad bagi masyarakat daerah pelosok.
                </p>
            </div>

            <!-- Kegiatan 5 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-xl text-red-600">
                    📦
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Tanggap Darurat Bencana</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Pemberian bantuan sembako, selimut, dan obat-obatan darurat bagi korban yang terdampak bencana alam secara cepat.
                </p>
            </div>

            <!-- Kegiatan 6 -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-xl text-purple-600">
                    🕌
                </div>
                <h3 class="font-bold text-slate-900 text-lg">Sarana Prasarana Dakwah</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Bantuan renovasi fasilitas wudhu, karpet sajadah, dan Al-Qur'an untuk masjid-masjid pedesaan yang membutuhkan pembenahan.
                </p>
            </div>
        </div>
    </section>

    <!-- 4. SEKSI PROGRAM INFAK EKSTRA (REAL-TIME DARI DATABASE - REFERENSI image_47b9f9.jpg) -->
    <section class="bg-white border-y border-slate-100 py-20 px-6">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center space-y-2">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Program Infak Ekstra Aktif</h2>
                <p class="text-sm text-slate-500">Ikut berkontribusi secara langsung dalam program-program kebaikan berikut.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($daftarProgramEkstra as $prog)
                    <div class="bg-white border border-slate-100 rounded-2xl shadow-xs p-6 space-y-4">
                        <span class="inline-block bg-emerald-50 text-emerald-600 font-extrabold text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-md">
                            Aktif
                        </span>
                        <h3 class="font-bold text-slate-800 text-lg leading-tight">
                            {{ $prog->name ?? ($prog->title ?? 'Program Infak Ekstra') }}
                        </h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($prog->description ?? 'Mari berkontribusi dalam program kebaikan ini.', 100) }}
                        </p>
                        <div class="space-y-2">
                            @php 
                                $persen = $prog->target_amount > 0 ? ($prog->current_amount / $prog->target_amount) * 100 : 0;
                                $persen = $persen > 100 ? 100 : $persen;
                            @endphp
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-[#0b6e3f] h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] font-medium text-slate-400">
                                <span>Terkumpul: <b class="text-slate-600">Rp {{ number_format($prog->current_amount, 0, ',', '.') }}</b></span>
                                <span>{{ round($persen, 1) }}%</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">Target: Rp {{ number_format($prog->target_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback jika database kosong (Akan otomatis hilang jika ada data asli dimasukkan) -->
                    <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
                        <p class="font-medium">Belum ada penggalangan program ekstra yang dibuka saat ini di database.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 5. CTA SECTION -->
    <section class="bg-gradient-to-t from-[#05331d] to-[#0b6e3f] text-white py-16 px-6 text-center">
        <div class="max-w-4xl mx-auto space-y-6">
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Bergabung dengan Keluarga Alumni FK Unpad</h2>
            <p class="text-emerald-100/70 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                Bersama kita bisa memberikan dampak yang lebih besar. Daftarkan diri sebagai anggota IKRA dan mulai berinfak hari ini.
            </p>
            <div class="pt-2">
                <a href="/daftar" class="bg-white text-[#0b6e3f] font-bold text-sm px-6 py-3.5 rounded-xl hover:bg-slate-50 shadow-md inline-block transition">
                    Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

</body>
</html>