<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Cinema Edukasi - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between hidden md:flex shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 bg-emerald-600 text-white font-medium p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>
                    <a href="{{ route('operational.pencairan') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="flex-1 p-10 max-w-5xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('operational.schedule', ['tab' => 'cinema']) }}" class="text-xs font-bold text-emerald-600 hover:underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Kalender Agenda
                </a>
                <h1 class="text-2xl font-black text-slate-900 mt-2">🎬 Penjadwalan & Pengajuan Dana Cinema Edukasi</h1>
                <p class="text-xs text-slate-500 mt-1">Input rincian kegiatan cinema edukasi beserta estimasi anggaran untuk diajukan ke bagian keuangan.</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-100 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-6 text-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl mb-6 text-sm">
                    <p class="font-bold mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('operational.cinema.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- SECTION 1: Detail Materi & Peserta --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-film text-purple-500"></i> Detail Kegiatan Cinema Edukasi
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama / Judul Materi</label>
                            <input type="text" name="nama_materi" value="{{ old('nama_materi') }}"
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition"
                                   placeholder="Contoh: Belajar Mengenal Tumbuhan, Siklus Hujan, dll." required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pengajar / Pembawa Materi</label>
                            <input type="text" name="pengajar" value="{{ old('pengajar') }}"
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500"
                                   placeholder="Nama pengajar atau fasilitator" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Penerima Manfaat (Audiens)</label>
                            <input type="text" name="penerima_manfaat" value="{{ old('penerima_manfaat') }}"
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500"
                                   placeholder="Contoh: Siswa SD Kelas 3 SDN 01 Bandung" required>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: Jadwal Kegiatan --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-clock text-purple-500"></i> Jadwal Pelaksanaan
                    </h2>
                    <div class="max-w-sm">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal & Waktu Kegiatan</label>
                        <input type="datetime-local" id="jadwalKegiatanInput" name="jadwal_kegiatan" value="{{ old('jadwal_kegiatan') }}"
                               class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-purple-500" required>
                        <p id="date_error_message" class="text-[11px] text-rose-600 font-bold mt-1.5 hidden flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Jadwal bentrok! Tanggal ini sudah digunakan program lain.
                        </p>
                    </div>
                </div>

                {{-- SECTION 3: Pengajuan Anggaran --}}
                <div class="bg-purple-50 border border-purple-100 rounded-2xl p-6 space-y-5">
                    <h2 class="text-xs font-bold text-purple-800 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-transfer text-purple-600"></i> Pengajuan Anggaran Kegiatan
                    </h2>
                    <div class="grid grid-cols-1 gap-5">
                        <div class="max-w-md">
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-bold text-purple-900">Total Nominal Dana yang Diajukan (Rp)</label>
                                <span class="text-[10px] font-bold text-purple-700 bg-purple-100/60 px-2 py-0.5 rounded-md">Dana Tersedia: Rp{{ number_format($danaTersedia, 0, ',', '.') }}</span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-sm font-bold text-purple-700">Rp</span>
                                <input type="number" id="amount_requested" name="amount_requested" value="{{ old('amount_requested') }}"
                                       class="w-full p-3 pl-12 bg-white border border-purple-200 rounded-xl text-sm font-black text-slate-800 focus:outline-none focus:ring-4 focus:ring-purple-500/10"
                                       placeholder="0" required>
                            </div>
                            <p id="error_message" class="text-xs text-rose-600 font-bold mt-1.5 hidden flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Permintaan melebihi ketersediaan dana program!
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-purple-900 mb-1.5">Rincian Penggunaan Dana</label>
                            <textarea name="description" rows="3"
                                      class="w-full p-3 bg-white border border-purple-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/10"
                                      placeholder="Contoh: Sewa proyektor Rp 200.000, Konsumsi peserta Rp 300.000, Transport pengajar Rp 100.000" required>{{ old('description') }}</textarea>
                            <p class="text-[10px] text-purple-600 mt-2 italic">*Estimasi biaya operasional lapangan untuk kegiatan cinema edukasi.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('operational.schedule', ['tab' => 'cinema']) }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold px-6 py-3 rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" id="submit_button" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i> Simpan & Ajukan ke Keuangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const amountInput = document.getElementById("amount_requested");
            const dateInput = document.getElementById("jadwalKegiatanInput");
            const errorMessage = document.getElementById("error_message");
            const dateErrorMessage = document.getElementById("date_error_message");
            const submitBtn = document.getElementById("submit_button");
            
            const maxBudget = {{ $danaTersedia }};
            const busyDates = @json($busyDates ?? []);

            let isBudgetOk = true;
            let isDateOk = true;

            function validateForm() {
                // 1. Validasi Budget
                const value = parseFloat(amountInput.value) || 0;
                if (value > maxBudget) {
                    errorMessage.classList.remove("hidden");
                    amountInput.classList.remove("border-purple-200", "focus:ring-purple-500/10", "focus:border-purple-500");
                    amountInput.classList.add("border-rose-400", "focus:ring-rose-500/10", "focus:border-rose-500", "bg-rose-50/30");
                    isBudgetOk = false;
                } else {
                    errorMessage.classList.add("hidden");
                    amountInput.classList.remove("border-rose-400", "focus:ring-rose-500/10", "focus:border-rose-500", "bg-rose-50/30");
                    amountInput.classList.add("border-purple-200", "focus:ring-purple-500/10", "focus:border-purple-500");
                    isBudgetOk = true;
                }

                // 2. Validasi Tanggal Bentrok
                if (dateInput.value) {
                    const selectedDateTime = dateInput.value; // Format: YYYY-MM-DDTHH:MM
                    const selectedDate = selectedDateTime.split('T')[0]; // Format: YYYY-MM-DD

                    if (busyDates.includes(selectedDate)) {
                        dateErrorMessage.classList.remove("hidden");
                        dateInput.classList.remove("border-slate-200", "focus:border-purple-500");
                        dateInput.classList.add("border-rose-400", "focus:border-rose-500", "bg-rose-50/30");
                        isDateOk = false;
                    } else {
                        dateErrorMessage.classList.add("hidden");
                        dateInput.classList.remove("border-rose-400", "focus:border-rose-500", "bg-rose-50/30");
                        dateInput.classList.add("border-slate-200", "focus:border-purple-500");
                        isDateOk = true;
                    }
                } else {
                    isDateOk = true;
                }

                // 3. Terapkan status tombol submit
                if (isBudgetOk && isDateOk) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                    submitBtn.classList.add("bg-slate-900", "hover:bg-slate-800", "cursor-pointer");
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove("bg-slate-900", "hover:bg-slate-800", "cursor-pointer");
                    submitBtn.classList.add("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                }
            }

            amountInput.addEventListener("input", validateForm);
            dateInput.addEventListener("change", validateForm);
        });
    </script>
</body>
</body>
</html>
