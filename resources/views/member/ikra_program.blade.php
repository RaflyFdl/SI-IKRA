@extends('member.member_layout')

@section('title', 'Program IKRA')

@section('member_content')
<div class="space-y-8">

    {{-- ======================================================== --}}
    {{-- HEADER HALAMAN --}}
    {{-- ======================================================== --}}
    <div class="p-6 rounded-2xl bg-gradient-to-r from-[#0b6e3f] to-[#0e9456] text-white shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight">Program IKRA</h1>
                <p class="text-emerald-100 text-sm leading-relaxed">
                    Pantau seluruh program yang dijalankan oleh IKRA — baik penyaluran infak reguler maupun program ekstra / penggalangan dana khusus.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="bg-white/20 border border-white/30 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider">
                    📋 Total Program: {{ $programReguler->count() + $programEkstra->count() }}
                </span>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- SEKSI 1: PROGRAM REGULER --}}
    {{-- ======================================================== --}}
    <div class="space-y-4">

        <div class="flex items-center gap-3">
            <div class="w-1 h-6 bg-emerald-600 rounded-full"></div>
            <h2 class="text-lg font-bold text-slate-800">Program Penyaluran Reguler</h2>
            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-emerald-200">
                {{ $programReguler->count() }} Program
            </span>
        </div>

        <p class="text-xs text-slate-500 -mt-2 pl-4">
            Program penyaluran infak bulanan yang direncanakan, sedang dilaksanakan, atau sudah diselesaikan oleh tim operasional IKRA.
        </p>

        @if($programReguler->isEmpty())
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl mb-3">📭</div>
                <p class="font-semibold text-slate-600 text-sm">Belum Ada Program Reguler</p>
                <p class="text-xs text-slate-400 mt-1">Tim operasional belum mengajukan program penyaluran untuk periode ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($programReguler as $reguler)
                    @php
                        // Tentukan label & warna status program reguler
                        switch ($reguler->status) {
                            case 'diajukan':
                                $statusLabel = 'Direncanakan';
                                $statusIcon  = '📋';
                                $statusBg    = 'bg-slate-100 text-slate-600 border-slate-200';
                                $cardBorder  = 'border-slate-200';
                                $badgeStrip  = 'bg-slate-400';
                                break;
                            case 'disetujui':
                                $statusLabel = 'Sedang Dilaksanakan';
                                $statusIcon  = '🔵';
                                $statusBg    = 'bg-blue-50 text-blue-700 border-blue-200';
                                $cardBorder  = 'border-blue-200';
                                $badgeStrip  = 'bg-blue-500';
                                break;
                            case 'dicairkan':
                                $statusLabel = 'Sedang Dilaksanakan';
                                $statusIcon  = '🔵';
                                $statusBg    = 'bg-blue-50 text-blue-700 border-blue-200';
                                $cardBorder  = 'border-blue-200';
                                $badgeStrip  = 'bg-blue-500';
                                break;
                            case 'dilaporkan':
                                $statusLabel = 'Program Selesai';
                                $statusIcon  = '✅';
                                $statusBg    = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $cardBorder  = 'border-emerald-200';
                                $badgeStrip  = 'bg-emerald-500';
                                break;
                            default:
                                $statusLabel = ucfirst($reguler->status ?? 'Direncanakan');
                                $statusIcon  = '📌';
                                $statusBg    = 'bg-amber-50 text-amber-700 border-amber-200';
                                $cardBorder  = 'border-amber-200';
                                $badgeStrip  = 'bg-amber-400';
                                break;
                        }
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $cardBorder }} shadow-sm overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col">

                        {{-- Strip warna status di atas --}}
                        <div class="h-1.5 w-full {{ $badgeStrip }}"></div>

                        <div class="p-5 flex flex-col gap-4 flex-1">

                            {{-- Header: status & periode --}}
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border uppercase tracking-wider {{ $statusBg }}">
                                    {{ $statusIcon }} {{ $statusLabel }}
                                </span>
                                @if($reguler->periode_bulan)
                                    <span class="text-[10px] text-slate-400 font-medium bg-slate-50 border border-slate-100 px-2 py-1 rounded-lg whitespace-nowrap">
                                        📅 {{ \Carbon\Carbon::parse($reguler->periode_bulan . '-01')->translatedFormat('F Y') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Nama program --}}
                            <div class="space-y-1">
                                <h3 class="font-bold text-slate-800 text-base leading-snug line-clamp-2">
                                    {{ $reguler->nama_program }}
                                </h3>
                                @if($reguler->penerima_manfaat)
                                    <p class="text-xs text-slate-500 flex items-center gap-1">
                                        <span>👥</span>
                                        <span>Penerima manfaat: {{ $reguler->penerima_manfaat }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Nominal --}}
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-center justify-between">
                                <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Dana Diajukan</span>
                                <span class="font-bold text-slate-800 text-sm">
                                    Rp {{ number_format($reguler->nominal_diajukan, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Tanggal pelaksanaan (jika ada) --}}
                            @if($reguler->tanggal_pelaksanaan)
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span class="text-sm">🗓️</span>
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-medium tracking-wider block">Tanggal Pelaksanaan</span>
                                        <span class="font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($reguler->tanggal_pelaksanaan)->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- SEKSI 2: PROGRAM EKSTRA --}}
    {{-- ======================================================== --}}
    <div class="space-y-4">

        <div class="flex items-center gap-3">
            <div class="w-1 h-6 bg-amber-500 rounded-full"></div>
            <h2 class="text-lg font-bold text-slate-800">Program Infak Ekstra</h2>
            <span class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-amber-200">
                {{ $programEkstra->count() }} Program
            </span>
        </div>

        <p class="text-xs text-slate-500 -mt-2 pl-4">
            Program penggalangan dana khusus (Infak Ekstra / Donasi Umum). Statusnya mulai dari penggalangan dana, proses pelaksanaan, hingga selesai.
        </p>

        @if($programEkstra->isEmpty())
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl mb-3">🎁</div>
                <p class="font-semibold text-slate-600 text-sm">Belum Ada Program Ekstra</p>
                <p class="text-xs text-slate-400 mt-1">Admin belum mempublikasikan program infak ekstra saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($programEkstra as $ekstra)
                    @php
                        $now        = \Carbon\Carbon::now();
                        $endDate    = $ekstra->end_date ? \Carbon\Carbon::parse($ekstra->end_date) : null;
                        $execDate   = $ekstra->execution_date ? \Carbon\Carbon::parse($ekstra->execution_date) : null;
                        $daysLeft   = $endDate ? $now->diffInDays($endDate, false) : null;
                        $percentage = $ekstra->target_amount > 0
                            ? min(round(($ekstra->current_amount / $ekstra->target_amount) * 100), 100)
                            : 0;

                        // Tentukan status ekstra berdasarkan field status & tanggal
                        if ($ekstra->status === 'completed') {
                            $statusLabel = 'Program Selesai';
                            $statusIcon  = '✅';
                            $statusBg    = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            $cardBorder  = 'border-emerald-200';
                            $badgeStrip  = 'bg-emerald-500';
                        } elseif ($ekstra->status === 'active' && $execDate && $now->greaterThanOrEqualTo($execDate)) {
                            // Sudah melewati tanggal pelaksanaan -> sedang dilaksanakan
                            $statusLabel = 'Sedang Dilaksanakan';
                            $statusIcon  = '🔵';
                            $statusBg    = 'bg-blue-50 text-blue-700 border-blue-200';
                            $cardBorder  = 'border-blue-200';
                            $badgeStrip  = 'bg-blue-500';
                        } elseif ($ekstra->status === 'active' && $endDate && $daysLeft !== null && $daysLeft <= 0) {
                            // Batas akhir penggalangan dana habis
                            $statusLabel = 'Batas Dana Habis';
                            $statusIcon  = '⏰';
                            $statusBg    = 'bg-rose-50 text-rose-700 border-rose-200';
                            $cardBorder  = 'border-rose-200';
                            $badgeStrip  = 'bg-rose-500';
                        } else {
                            // Default: masih dalam masa penggalangan dana
                            $statusLabel = 'Penggalangan Dana';
                            $statusIcon  = '🟡';
                            $statusBg    = 'bg-amber-50 text-amber-700 border-amber-200';
                            $cardBorder  = 'border-amber-200';
                            $badgeStrip  = 'bg-amber-400';
                        }
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $cardBorder }} shadow-sm overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col">

                        {{-- Strip warna status di atas --}}
                        <div class="h-1.5 w-full {{ $badgeStrip }}"></div>

                        {{-- Gambar (jika ada) --}}
                        @if($ekstra->image_path)
                            <div class="w-full h-36 overflow-hidden border-b border-slate-100">
                                <img src="{{ asset('storage/' . $ekstra->image_path) }}"
                                     alt="{{ $ekstra->name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x300/f1f5f9/047857?text=Program+Ekstra';">
                            </div>
                        @endif

                        <div class="p-5 flex flex-col gap-4 flex-1">

                            {{-- Header: status & countdown --}}
                            <div class="flex items-start justify-between gap-2 flex-wrap">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border uppercase tracking-wider {{ $statusBg }}">
                                    {{ $statusIcon }} {{ $statusLabel }}
                                </span>
                                @if($ekstra->status === 'active' && $daysLeft !== null && $daysLeft > 0 && $ekstra->status !== 'completed')
                                    <span class="text-[10px] text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2 py-1 rounded-lg whitespace-nowrap">
                                        ⏳ {{ ceil($daysLeft) }} hari lagi
                                    </span>
                                @endif
                            </div>

                            {{-- Nama & deskripsi program --}}
                            <div class="space-y-1.5">
                                <h3 class="font-bold text-slate-800 text-base leading-snug line-clamp-2">
                                    {{ $ekstra->name }}
                                </h3>
                                <p class="text-xs text-slate-400 leading-relaxed line-clamp-2">
                                    {{ $ekstra->description }}
                                </p>
                            </div>

                            {{-- Progress Dana --}}
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-medium tracking-wider block">Terkumpul</span>
                                        <strong class="text-slate-700 font-bold text-sm">
                                            Rp {{ number_format($ekstra->current_amount, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 uppercase font-medium tracking-wider block">Target</span>
                                        <strong class="text-slate-500 font-semibold">
                                            Rp {{ number_format($ekstra->target_amount, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700
                                        @if($ekstra->status === 'completed') bg-emerald-500
                                        @elseif($percentage >= 75) bg-emerald-500
                                        @elseif($percentage >= 40) bg-amber-400
                                        @else bg-rose-400
                                        @endif"
                                        style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                                <div class="text-right text-[10px] font-bold
                                    @if($percentage >= 75) text-emerald-600
                                    @elseif($percentage >= 40) text-amber-600
                                    @else text-rose-500
                                    @endif">
                                    {{ $percentage }}% Terpenuhi
                                </div>
                            </div>

                            {{-- Tanggal pelaksanaan (jika ada) --}}
                            @if($ekstra->execution_date)
                                <div class="flex items-center gap-2 text-xs bg-slate-50 border border-slate-100 rounded-xl p-3">
                                    <span>🗓️</span>
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-medium tracking-wider block">Tanggal Pelaksanaan</span>
                                        <span class="font-semibold text-slate-700">
                                            {{ \Carbon\Carbon::parse($ekstra->execution_date)->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- Dokumentasi (jika program selesai dan ada dokumentasi) --}}
                            @if($ekstra->status === 'completed' && $ekstra->documentation_path)
                                <div class="mt-auto">
                                    <a href="{{ asset('storage/' . $ekstra->documentation_path) }}"
                                       target="_blank"
                                       class="flex items-center gap-2 text-xs text-emerald-700 font-semibold hover:text-emerald-900 bg-emerald-50 border border-emerald-100 rounded-xl p-3 hover:bg-emerald-100 transition">
                                        <span>📸</span>
                                        <span>Lihat Dokumentasi Program</span>
                                    </a>
                                </div>
                            @endif

                            {{-- CTA: Infak sekarang (hanya jika aktif penggalangan) --}}
                            @if($ekstra->status === 'active')
                                <div class="mt-auto pt-2">
                                    <a href="{{ route('member.extra.show', $ekstra->id) }}"
                                       class="block text-center w-full bg-[#0b6e3f] hover:bg-[#095c34] text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all duration-200">
                                        Infak Sekarang →
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- LEGENDA STATUS --}}
    {{-- ======================================================== --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
        <h3 class="font-bold text-slate-700 text-sm mb-4">📌 Keterangan Status Program</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">Program Reguler</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                        <span><strong>Direncanakan</strong> — Program sudah diajukan, menunggu persetujuan Pembina</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                        <span><strong>Sedang Dilaksanakan</strong> — Program disetujui & dana sudah dicairkan</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                        <span><strong>Program Selesai</strong> — Laporan realisasi sudah diterima keuangan</span>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">Program Ekstra</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                        <span><strong>Penggalangan Dana</strong> — Program sedang membuka donasi dari anggota</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                        <span><strong>Sedang Dilaksanakan</strong> — Sudah melewati tanggal pelaksanaan</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                        <span><strong>Program Selesai</strong> — Program telah selesai dilaksanakan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
