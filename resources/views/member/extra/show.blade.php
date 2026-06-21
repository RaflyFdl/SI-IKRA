@extends('member.member_layout')

@section('title', 'Detail Program Infak')

@section('member_content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div>
        <a href="{{ route('member.programs.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-emerald-600 transition duration-200">
            ⬅️ Kembali ke Daftar Program
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        
        <div class="w-full h-64 bg-slate-100 relative flex items-center justify-center border-b border-slate-100">
            @if($program->image_path)
                <img src="{{ asset('storage/' . $program->image_path) }}" 
                     alt="{{ $program->name }}" 
                     class="w-full h-full object-cover block">
            @else
                <div class="text-6xl">🎁</div>
            @endif
            
            <span class="absolute top-4 left-4 text-xs font-bold px-3 py-1 rounded-lg uppercase tracking-wide bg-emerald-600 text-white shadow">
                {{ $program->category ?? 'Donasi Umum' }}
            </span>
        </div>

        <div class="p-6 space-y-4">
            <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $program->name }}</h1>
            
            <div class="text-sm text-slate-600 leading-relaxed space-y-2 whitespace-pre-line">
                {{ $program->description }}
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-emerald-50 to-slate-50 border border-emerald-100 rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Infak Sekarang</h3>
        <p class="text-xs text-slate-500 mb-4">Masukkan jumlah nominal kontribusi infak ekstra terbaik Anda untuk program ini.</p>
        
        <form action="{{ route('member.extra.checkout', $program->id) }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="nominal" class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Nominal Infak (Rp)</label>
                <div class="relative mt-1 rounded-xl shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <span class="text-slate-500 font-bold text-lg">Rp</span>
                    </div>
                    <input type="number" name="nominal" id="nominal" min="10000" step="1000"
                           class="block w-full rounded-xl border-gray-200 pl-12 pr-4 py-3 text-lg font-bold text-slate-800 placeholder-gray-300 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Contoh: 50000" required>
                </div>
                @error('nominal')
                    <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-2">*Minimal infak adalah Rp 10.000</p>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold py-3.5 px-4 rounded-xl shadow transition-all duration-200 cursor-pointer text-center block">
                Lanjut Pembayaran
            </button>
        </form>
    </div>

</div>
@endsection