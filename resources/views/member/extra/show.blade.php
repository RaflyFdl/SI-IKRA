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
            
            @if($program->target_amount > 0)
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100 flex justify-between items-center shadow-sm">
                <div>
                    <span class="block text-[11px] font-bold text-emerald-600 uppercase tracking-wider mb-1">🎯 Target Penggalangan Dana</span>
                    <span class="text-lg font-bold text-emerald-800">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
            
            <div class="text-sm text-slate-600 leading-relaxed space-y-2 whitespace-pre-line">
                {{ $program->description }}
            </div>

            @if($program->detailKebutuhan && $program->detailKebutuhan->count() > 0)
                <div class="mt-6 border-t border-slate-100 pt-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-600"></i> Rincian Kebutuhan Dana
                    </h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500">
                                    <th class="p-3 font-semibold w-12 text-center border-b border-slate-200">No</th>
                                    <th class="p-3 font-semibold border-b border-slate-200">Kebutuhan</th>
                                    <th class="p-3 font-semibold text-center border-b border-slate-200">Jumlah</th>
                                    <th class="p-3 font-semibold text-right border-b border-slate-200">Estimasi Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php $totalEstimasi = 0; @endphp
                                @foreach($program->detailKebutuhan as $index => $detail)
                                    @php $totalEstimasi += $detail->harga; @endphp
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="p-3 text-center text-slate-500">{{ $index + 1 }}</td>
                                        <td class="p-3 font-medium text-slate-700">{{ $detail->nama_barang }}</td>
                                        <td class="p-3 text-center text-slate-600">{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                                        <td class="p-3 text-right font-medium text-slate-800">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                                    <td colspan="3" class="p-3 text-right">Total Estimasi</td>
                                    <td class="p-3 text-right text-emerald-700">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
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

            <div>
                <label for="bank_code" class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Pilih Bank Pembayaran</label>
                <div class="relative mt-1 rounded-xl shadow-sm">
                    <select name="bank_code" id="bank_code" required 
                            class="block w-full rounded-xl border-gray-200 px-4 py-3 text-sm font-bold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                        <option value="" disabled selected>-- Pilih Bank --</option>
                        <option value="BCA">Bank Central Asia (BCA)</option>
                        <option value="MANDIRI">Bank Mandiri</option>
                        <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                        <option value="BNI">Bank Negara Indonesia (BNI)</option>
                        <option value="PERMATA">Bank Permata</option>
                        <option value="MUAMALAT">Bank Muamalat (Syariah)</option>
                    </select>
                </div>
                @error('bank_code')
                    <p class="text-xs text-rose-600 font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-base font-bold py-3.5 px-4 rounded-xl shadow transition-all duration-200 cursor-pointer text-center block mt-2">
                Lanjut Pembayaran
            </button>
        </form>
    </div>

</div>
@endsection