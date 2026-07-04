<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA System - Verifikasi Pendaftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f6f9] min-h-screen flex text-slate-800 font-sans">

    <aside class="w-64 bg-[#111827] text-slate-300 flex flex-col min-h-screen sticky top-0 shadow-xl">
        <div class="p-5 flex items-center gap-3 border-b border-slate-800">
            <div class="bg-emerald-500 text-white font-bold p-2 rounded-xl text-center min-w-[40px]">IK</div>
            <div>
                <h1 class="font-bold text-white leading-tight">IKRA System</h1>
                <p class="text-xs text-slate-400">Admin</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-2 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-600 text-white transition">
                <i class="fa-solid stroke-2 fa-user-check text-lg"></i>
                <span>Verifikasi Pendaftar</span>
            </a>
            <a href="{{ route('admin.programs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-bullhorn text-lg"></i>
                <span>Publikasi Program</span>
            </a>
            
            <div class="pt-4 pb-1 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Data Master (CRUD)</div>
            
            <a href="{{ route('admin.member.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-users-gear text-lg"></i>
                <span>Master Anggota</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-folder-tree text-lg"></i>
                <span>Master Program Ekstra</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-800 text-xs text-slate-500 text-center">
            &copy; 2026 IKRA Padjadjaran
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">👥 Verifikasi Pendaftar (Rutin)</h2>
                <p class="text-sm text-slate-500">Verifikasi Anggota & Pembuatan VA Otomatis - Yayasan IKRA Padjadjaran</p>
            </div>
            <a href="{{ route('register') }}" class="border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-sm" target="_blank">
                Buka Form Daftar ↗
            </a>
        </header>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#111827] text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800">
                            <th class="p-4">Nama / Email</th>
                            <th class="p-4">Angkatan</th>
                            <th class="p-4">No. WhatsApp</th>
                            <th class="p-4">Bukti</th>
                            <th class="p-4">Status Akun</th>
                            <th class="p-4">VA Muamalat</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($members as $m)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $m->nama }}</div>
                                <span class="text-xs text-slate-400 font-mono">{{ $m->email }}</span>
                            </td>
                            <td class="p-4">
                                <span class="inline-block bg-slate-100 text-slate-700 font-semibold text-xs px-2.5 py-1 rounded-lg border border-slate-200">{{ $m->angkatan }}</span>
                            </td>
                            <td class="p-4 font-medium text-slate-600">{{ $m->no_wa }}</td>
                            <td class="p-4">
                                <a href="{{ asset('uploads/' . $m->bukti_pendukung) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-sky-50 text-sky-700 border border-sky-200 font-semibold text-xs px-3 py-1.5 rounded-xl hover:bg-sky-100 transition">
                                    <i class="fa-regular fa-image"></i> Lihat Bukti
                                </a>
                            </td>
                            <td class="p-4">
                                @if($m->status == 'pending')
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold px-2.5 py-1 rounded-lg">Pending</span>
                                @elseif($m->status == 'active')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-lg">Aktif</span>
                                @else
                                    <span class="bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold px-2.5 py-1 rounded-lg">Ditolak</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-xs font-bold text-emerald-600 tracking-wide">
                                {{ $m->va_muamalat ?? 'Belum Tergenerate' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($m->status == 'pending')
                                    <div class="flex justify-center gap-2">
                                        <form action="{{ route('admin.approve', $m->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 py-2 rounded-xl transition shadow-sm">
                                                Setujui & Buat VA
                                            </button>
                                        </form>
                                        <button onclick="toggleModal('rejectModal{{ $m->id }}')" class="bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs px-3 py-2 rounded-xl transition shadow-sm">
                                            Tolak
                                        </button>
                                    </div>

                                    <div id="rejectModal{{ $m->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
                                        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden text-left animate-in fade-in zoom-in-95 duration-150">
                                            <div class="bg-rose-600 p-4 text-white flex justify-between items-center">
                                                <h5 class="font-bold">Tolak Pendaftaran: {{ $m->nama }}</h5>
                                                <button onclick="toggleModal('rejectModal{{ $m->id }}')" class="text-white/80 hover:text-white text-xl">&times;</button>
                                            </div>
                                            <form action="{{ route('admin.member.reject', $m->id) }}" method="POST" class="p-6">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Alasan Penolakan Berkas/Akun:</label>
                                                    <textarea name="alasan" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="Contoh: Bukti identitas KTM buram, mohon upload ulang berkas asli." required></textarea>
                                                    <p class="text-[11px] text-slate-400 mt-1.5">Alasan ini akan otomatis terkirim ke alamat email pendaftar.</p>
                                                </div>
                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" onclick="toggleModal('rejectModal{{ $m->id }}')" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-md">Kirim Email & Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <button class="text-xs font-bold text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl cursor-not-allowed" disabled>Selesai</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 font-medium">Belum ada alumni yang mendaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }
    </script>
</body>
</html>