@extends('member.member_layout')

@section('title', 'Jelajah Infak Ekstra')

@section('member_content')
<div class="space-y-6">

    <div class="p-5 rounded-2xl border bg-slate-50 border-slate-200">
        <h2 class="text-xl font-bold text-slate-900">Program Infak Ekstra (Penggalangan Dana)</h2>
        <p class="text-xs text-slate-500 mt-1">
            Pilih program penggalangan dana di bawah ini untuk melihat informasi detail dan menyalurkan infak ekstra Anda secara real-time.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
            @php
                // Hitung persentase pemenuhan dana program (maksimal 100%) - LOGIC ASLI
                $percentage = $program->target_amount > 0 ? min(round(($program->current_amount / $program->target_amount) * 100), 100) : 0;
                
                // Perbaikan Deteksi Batas Waktu menggunakan Carbon (Mencegah salah hitung pada Program Terbuka)
                $daysLeft = null;
                if ($program->end_date) {
                    $endDate = \Carbon\Carbon::parse($program->end_date);
                    $daysLeft = \Carbon\Carbon::now()->diffInDays($endDate, false);
                }
            @endphp
            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-300">
                
                <div>
                    <div class="w-full h-44 bg-slate-100 relative overflow-hidden border-b border-slate-100 flex items-center justify-center">
                        @if($program->image_path)
                            <img src="{{ asset('storage/' . $program->image_path) }}" 
                                 alt="{{ $program->name }}" 
                                 class="w-full h-full object-cover block"
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/f1f5f9/047857?text=Foto+Program';">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-emerald-50 to-slate-50 text-emerald-700 p-4 text-center">
                                <span class="text-4xl mb-1">🎁</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $program->category ?? 'Donasi Umum' }}</span>
                            </div>
                        @endif

                        <span class="absolute top-3 left-3 text-[9px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide shadow-sm {{ $program->category == 'Podcast' ? 'bg-blue-600 text-white' : ($program->category == 'Cinema Edukasi' ? 'bg-purple-600 text-white' : 'bg-emerald-600 text-white') }}">
                            {{ $program->category ?? 'Donasi Umum' }}
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center text-[11px] font-bold">
                            <div>
                                @if(in_array($program->category, ['Podcast', 'Cinema Edukasi']))
                                    {{-- Program berkelanjutan: tidak menampilkan countdown hari --}}
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg border border-blue-200/60 uppercase tracking-wider inline-flex items-center gap-1">
                                        ♾️ Terbuka Terus
                                    </span>
                                @elseif($program->end_date)
                                    @if($daysLeft > 0)
                                        <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg border border-amber-200/60 uppercase tracking-wider">
                                            ⏳ {{ ceil($daysLeft) }} Hari Lagi
                                        </span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-lg border border-rose-200/60 uppercase tracking-wider">
                                            Hari Ini Batas Akhir
                                        </span>
                                    @endif
                                @else
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg border border-blue-200/60 uppercase tracking-wider inline-flex items-center gap-1">
                                        ♾️ Terbuka Terus
                                    </span>
                                @endif
                            </div>

                            {{-- Persentase terpenuhi hanya untuk Donasi Umum --}}
                            @if($program->category == 'Donasi Umum')
                                <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                                    {{ $percentage }}% Terpenuhi
                                </span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <h3 class="font-bold text-slate-800 text-base leading-snug hover:text-indigo-600 transition duration-200 line-clamp-2 min-h-[3rem]">{{ $program->name }}</h3>
                            <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                {{ $program->description }}
                            </p>
                        </div>

                        {{-- Tanggal pelaksanaan hanya ditampilkan untuk Donasi Umum (program momentum) --}}
                        @if($program->execution_date && $program->category == 'Donasi Umum')
                            <div class="p-2.5 bg-emerald-50/60 border border-emerald-100 rounded-xl flex items-center gap-2">
                                <span class="text-sm">📅</span>
                                <div class="text-[11px]">
                                    <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Tanggal Pelaksanaan</span>
                                    <span class="text-emerald-800 font-bold">{{ \Carbon\Carbon::parse($program->execution_date)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Progress bar & target dana hanya untuk Donasi Umum --}}
                        @if($program->category == 'Donasi Umum')
                            <div class="space-y-2 pt-2">
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-xs pt-1">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-medium">Terkumpul</span>
                                        <strong class="text-slate-700 font-bold">Rp {{ number_format($program->current_amount, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-400 block text-[10px] uppercase font-medium">Target Dana</span>
                                        <strong class="text-slate-500 font-semibold">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Podcast & Cinema: hanya tampilkan dana terkumpul, tanpa target --}}
                            <div class="pt-2">
                                <div class="flex items-center justify-between text-xs">
                                    <div>
                                        <span class="text-slate-400 block text-[10px] uppercase font-medium">Dana Terkumpul</span>
                                        <strong class="text-emerald-700 font-bold">Rp {{ number_format($program->current_amount, 0, ',', '.') }}</strong>
                                    </div>
                                    <span class="text-[10px] text-slate-400 italic">Tidak ada target</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-5 bg-slate-50 border-t border-gray-100">
                    <a href="{{ route('member.extra.show', $program->id) }}" class="block text-center w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all duration-200 cursor-pointer">
                        Lihat Detail & Infak Sekarang
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 text-slate-400 text-sm bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-3xl mb-2">❤️</div>
                <p class="font-semibold text-slate-600">Belum Ada Program Aktif</p>
                <p class="text-xs text-slate-400 mt-1">Saat ini tidak ada program penggalangan dana infak ekstra yang sedang berjalan.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection