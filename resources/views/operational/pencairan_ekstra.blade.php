<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencairan Dana Ekstra - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between">
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
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 bg-emerald-600 text-white p-3 rounded-lg font-medium transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>

                    <a href="{{ route('operational.penyaluran-reguler.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.penyaluran-reguler.index') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-heart-circle-check w-5 text-center"></i>
                        <span>Penyaluran Infak Reguler</span>
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-700 pt-4">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 text-red-400 hover:bg-slate-800 p-3 rounded-lg transition text-left cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>

        <div class="flex-1 p-10 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Pengajuan Pencairan Dana Infak Ekstra</h2>
                    <p class="text-slate-500 mt-1">Pilih program aktif di bawah ini untuk meminta modal awal kerja ke bagian keuangan.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-sm">
                    <span>✅</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">Program Siap Salur</h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($programs as $program)
                        <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition hover:bg-slate-50/50">
                            <div class="space-y-2 max-w-xl">
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-base font-bold text-slate-900">{{ $program->name }}</h4>
                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">BELUM CAIR</span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ $program->description ?? 'Tidak ada deskripsi program.' }}</p>
                                <div class="text-xs text-slate-600 font-semibold">
                                    <i class="fa-solid fa-wallet text-slate-400 mr-1.5"></i>Rencana Anggaran (Dana Bersih): <span class="text-emerald-600 font-bold">Rp{{ number_format($program->dana_clean_ekstra ?? $program->dana_bersih_ekstra, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div>
                                <button onclick="openDisbursementModal('{{ $program->id }}', '{{ addslashes($program->name) }}', {{ $program->dana_clean_ekstra ?? $program->dana_bersih_ekstra ?? 0 }})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition shadow-sm inline-flex items-center space-x-2 cursor-pointer">
                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                    <span>Minta Pencairan Dana</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 px-4 space-y-3 bg-slate-50/50">
                            <span class="text-4xl block">📂</span>
                            <p class="text-slate-400 text-sm font-medium">Belum ada program infak ekstra baru yang membutuhkan pengajuan saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-10 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">📦 Status & Riwayat Pengajuan Dana</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                                <th class="p-4">Nama Program Extra</th>
                                <th class="p-4">Nominal Kerja</th>
                                <th class="p-4">Rekening Tujuan</th>
                                <th class="p-4 text-center">Status Sistem</th>
                                <th class="p-4 text-center">Aksi / Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatPengajuan as $pengajuan)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-bold text-slate-800">
                                        {{ $pengajuan->extraProgram->name ?? 'Program Tidak Ditemukan' }}
                                    </td>
                                    <td class="p-4 font-semibold text-slate-700">
                                        Rp{{ number_format($pengajuan->nominal_diminta, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-xs text-slate-600">
                                        <span class="font-bold block">{{ $pengajuan->nama_bank }}</span>
                                        <span class="font-mono text-slate-400">{{ $pengajuan->nomor_rekening }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($pengajuan->status === 'PENDING')
                                            <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">⏳ Menunggu Transfer Keuangan</span>
                                        @elseif($pengajuan->status === 'DICAIRKAN')
                                            <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">💸 Dana Cair / Butuh Laporan</span>
                                        @elseif($pengajuan->status === 'REIMBURSE_PENDING')
                                            <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-purple-50 text-purple-700 border border-purple-200">🚨 Nota Over / Ajukan Reimburse</span>
                                        @else
                                            <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✅ Selesai & Arsip</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($pengajuan->status === 'DICAIRKAN')
                                            <a href="{{ route('operational.laporan.form', $pengajuan->id) }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                ✍️ Input Nota Belanja
                                            </a>
                                        @elseif($pengajuan->status === 'PENDING')
                                            <span class="text-xs text-slate-400 italic">Proses Review...</span>
                                        @else
                                            <span class="text-xs text-emerald-600 font-semibold">Selesai 🏁</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-400 text-xs">
                                        Belum ada data pengajuan dalam riwayat pelaporan sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="disbursementModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden transform scale-95 transition-all">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-900 text-base">Form Permintaan Pencairan</h3>
                <button onclick="closeDisbursementModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">✕</button>
            </div>
            
            <form action="{{ route('operational.pencairan.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="program_id" id="modal_program_id">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Program</label>
                    <input type="text" id="modal_program_name" class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2.5 text-sm font-semibold text-slate-700" readonly>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nominal yang Diminta</label>
                    <input type="text" id="modal_program_amount" class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2.5 text-sm font-bold text-emerald-600" readonly>
                </div>

                <hr class="border-slate-100">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Bank / E-Wallet</label>
                    <input type="text" name="bank_name" placeholder="Misal: BCA, Mandiri, Dana" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-600" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rekening / No. HP</label>
                    <input type="text" name="account_number" placeholder="Masukkan nomor rekening tujuan" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-600" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="account_name" placeholder="Nama lengkap sesuai kartu/aplikasi" class="w-full border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-600" required>
                </div>

                <div class="pt-2 flex space-x-3">
                    <button type="button" onclick="closeDisbursementModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold py-3 rounded-lg transition cursor-pointer">Batal</button>
                    <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-lg transition shadow-sm cursor-pointer">Kirim ke Keuangan 🚀</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDisbursementModal(id, name, amount) {
            document.getElementById('modal_program_id').value = id;
            document.getElementById('modal_program_name').value = name;
            document.getElementById('modal_program_amount').value = 'Rp ' + amount.toLocaleString('id-ID');
            
            const modal = document.getElementById('disbursementModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDisbursementModal() {
            const modal = document.getElementById('disbursementModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>