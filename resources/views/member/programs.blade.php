@extends('member.member_layout')

@section('title', 'Jelajah Infak Ekstra')

@section('member_content')
<div class="space-y-6">

    <div class="p-5 rounded-2xl border bg-slate-50 border-slate-200">
        <h2 class="text-xl font-bold text-slate-900">Program Infak Ekstra (Penggalangan Dana)</h2>
        <p class="text-xs text-slate-500 mt-1">
            Salurkan donasi terbaik Anda langsung ke nomor Virtual Account khusus masing-masing program di bawah ini secara real-time.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
            @php
                // Hitung persentase pemenuhan dana program (maksimal 100%) - LOGIC ASLI
                $percentage = $program->target_amount > 0 ? min(round(($program->current_amount / $program->target_amount) * 100), 100) : 0;
                
                // Hitung sisa hari pengerjaan program menggunakan Carbon - LOGIC ASLI
                $endDate = \Carbon\Carbon::parse($program->end_date);
                $daysLeft = \Carbon\Carbon::now()->diffInDays($endDate, false);
            @endphp
            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-300">
                
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-center text-[11px] font-bold">
                        @if($daysLeft > 0)
                            <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg border border-amber-200/60 uppercase tracking-wider">
                                ⏳ {{ $daysLeft }} Hari Lagi
                            </span>
                        @else
                            <span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-lg border border-rose-200/60 uppercase tracking-wider">
                                Hari Ini Batas Akhir
                            </span>
                        @endif
                        
                        <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                            {{ $percentage }}% Terpenuhi
                        </span>
                    </div>

                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-800 text-base leading-snug hover:text-indigo-600 transition duration-200 line-clamp-2 min-h-[3rem]">{{ $program->name }}</h3>
                        <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $program->description }}
                        </p>
                    </div>

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
                </div>

                <div class="p-5 bg-slate-50/80 border-t border-gray-100 space-y-3">
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">No. VA Bank Muamalat (Anda)</span>
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between shadow-sm">
                            <div class="overflow-hidden mr-2">
                                <strong id="va-{{ $program->id }}" class="text-base font-mono text-indigo-600 tracking-wider block truncate">
                                    {{ $program->dynamic_va ?? 'Belum Aktif' }}
                                </strong>
                            </div>
                            
                            @if($program->dynamic_va && $program->dynamic_va !== 'Gagal Memuat VA')
                                <button onclick="copyToClipboard('{{ $program->dynamic_va }}', this)" class="bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-600 text-xs font-bold px-3 py-2 rounded-lg transition-all duration-200 focus:outline-none flex items-center gap-1 shrink-0 cursor-pointer">
                                    <span>Salin</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-normal italic">
                        *Silakan transfer ke nomor VA khusus Anda di atas. Layar ATM/m-Banking otomatis memunculkan nama Anda. Transaksi akan langsung terdata otomatis di riwayat sistem.
                    </p>
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

<script>
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.innerHTML;
            button.innerHTML = '<span>Tersalin!</span>';
            button.classList.remove('bg-slate-100', 'text-slate-600');
            button.classList.add('bg-emerald-500', 'text-white');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-emerald-500', 'text-white');
                button.classList.add('bg-slate-100', 'text-slate-600');
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin teks: ', err);
        });
    }
</script>
@endsection