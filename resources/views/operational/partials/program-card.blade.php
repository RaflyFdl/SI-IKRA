@php
    $percentage = $program->target_amount > 0 ? min(round(($program->current_amount / $program->target_amount) * 100), 100) : 0;
@endphp

<div class="border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition duration-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white shadow-sm">
    <div class="space-y-3 flex-1">
        <div class="flex flex-wrap items-center gap-3">
            @if($program->status === 'active')
                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">🔴 Sedang Berjalan</span>
            @else
                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 uppercase border border-emerald-200">🏁 Selesai & Diarsipkan</span>
            @endif
            <h3 class="text-lg font-bold text-slate-800">{{ $program->name }}</h3>
        </div>
        <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">{{ $program->description }}</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 text-xs">
            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Dana Terkumpul</span>
                <span class="font-bold text-slate-700 block">Rp {{ number_format($program->current_amount, 0, ',', '.') }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">({{ $percentage }}% Terpenuhi)</span>
            </div>
            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Target Pendanaan</span>
                <span class="font-bold text-slate-600 block pt-0.5">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</span>
            </div>
            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Batas Donasi</span>
                <span class="font-bold text-slate-600 block pt-0.5">📅 {{ $program->end_date ? \Carbon\Carbon::parse($program->end_date)->translatedFormat('d M Y') : 'Terbuka' }}</span>
            </div>
            <div class="bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100 text-slate-900">
                <span class="text-emerald-700 block font-bold uppercase text-[9px] tracking-wider">Tanggal Kerja Pelaksanaan</span>
                <span class="font-bold block pt-0.5">📅 {{ $program->execution_date ? \Carbon\Carbon::parse($program->execution_date)->translatedFormat('d F Y') : 'Belum Dijadwalkan' }}</span>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-auto shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 flex flex-col gap-3 min-w-[260px]">
        @if($program->status === 'active')
            <form action="{{ route('operational.update-date', $program->id) }}" method="POST" class="space-y-1">
                @csrf
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Set / Ubah Tanggal Kerja</label>
                <div class="flex gap-2">
                    <input type="date" name="execution_date" value="{{ $program->execution_date }}" class="p-2 border border-slate-200 rounded-lg text-xs w-full focus:outline-none focus:border-emerald-500" required>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm shrink-0 cursor-pointer">Simpan</button>
                </div>
            </form>

            @if($program->execution_date)
                <div class="pt-2 border-t border-slate-100">
                    <form action="{{ route('operational.complete', $program->id) }}" method="POST" enctype="multipart/form-data" class="space-y-1">
                        @csrf
                        <label class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Selesaikan Program & Upload Dokumentasi</label>
                        <div class="flex flex-col gap-1.5">
                            <input type="file" name="documentation" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer" required>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin program ini sudah sukses terlaksana?')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm text-center cursor-pointer">
                                🏁 Program Selesai (Arsipkan)
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @else
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center space-y-2">
                <span class="text-xs font-bold text-emerald-800 block">🏆 PROGRAM DINYATAKAN SELESAI</span>
                @if($program->documentation_path)
                    <a href="{{ asset('storage/' . $program->documentation_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                        🖼️ Lihat Bukti Dokumentasi Lapangan
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>