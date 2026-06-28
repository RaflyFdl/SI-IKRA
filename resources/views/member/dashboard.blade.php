@extends('member.member_layout')

@section('title', 'Infak Reguler')

@section('member_content')
<div class="space-y-6">

    @if($sudahBayarBulanIni)
        <div class="p-5 rounded-2xl border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-emerald-50/60 border-emerald-200">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-emerald-900">Selamat Datang, {{ $member->nama }}!</h2>
                <p class="text-xs text-emerald-700">Periode Bulan Berjalan: <span class="font-bold uppercase">{{ date('F Y') }}</span></p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="bg-emerald-600 text-white text-xs font-black px-4 py-2 rounded-xl shadow-xs uppercase tracking-wider">
                    ● Sudah Infak
                </span>
            </div>
        </div>
    @else
        <div class="p-5 rounded-2xl border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-rose-50/60 border-rose-200">
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-rose-900">Selamat Datang, {{ $member->nama }}!</h2>
                <p class="text-xs text-rose-700">Periode Bulan Berjalan: <span class="font-bold uppercase">{{ date('F Y') }}</span></p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="bg-rose-600 text-white text-xs font-black px-4 py-2 rounded-xl shadow-xs uppercase tracking-wider animate-pulse">
                    ● Belum Infak
                </span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-5 bg-gradient-to-br from-[#0b6e3f] to-[#074729] text-white p-6 rounded-2xl shadow-sm space-y-6 flex flex-col justify-between">
            <div class="space-y-2">
                <span class="bg-white/20 text-emerald-100 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide border border-white/10">
                    Muamalat Virtual Account Fixed
                </span>
                <h3 class="text-xl font-bold tracking-tight">Nomor Rekening VA Pribadi</h3>
                <p class="text-xs text-emerald-200/80 leading-relaxed">Gunakan nomor VA tetap ini untuk penyaluran otomatis komitmen infak bulanan reguler Anda.</p>
            </div>

            <div class="bg-black/20 backdrop-blur-xs p-4 rounded-xl border border-white/10 flex items-center justify-between">
                <span class="font-mono text-xl md:text-2xl font-bold tracking-widest text-emerald-300">{{ $member->va_muamalat }}</span>
                <button onclick="navigator.clipboard.writeText('{{ $member->va_muamalat }}'); alert('Nomor VA berhasil disalin!')" class="text-xs bg-white text-[#0b6e3f] px-2.5 py-1.5 rounded-lg font-bold cursor-pointer hover:bg-slate-100 active:scale-95 transition-all">
                    Salin
                </button>
            </div>

            <p class="text-[11px] text-emerald-200/70 italic">*Sistem otomatis mencatat transaksi atas nama Anda secara real-time.</p>
            @if(!$sudahBayarBulanIni)
                <div class="mt-4 border-t border-white/20 pt-4">
                    <form action="{{ route('simulation.regular', $member->id) }}" method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-white text-[#0b6e3f] font-bold py-3 rounded-xl hover:bg-gray-100 transition">
                            Simulasi Pembayaran
                        </button>
                    </form>
                                <p class="text-[10px] text-emerald-100 mt-2 text-center">
                        Tombol ini hanya tersedia pada mode Sandbox untuk simulasi pembayaran.
                    </p>
                </div>
                @else
                    <div class="mt-4 border-t border-white/20 pt-4">
                        <div class="bg-emerald-500/20 border border-emerald-300 rounded-xl p-3 text-center">
                            <div class="text-lg">✅</div>
                            <div class="text-sm font-bold text-white">
                                Pembayaran Bulan Ini Berhasil
                            </div>
                            <div class="text-xs text-emerald-100 mt-1">
                                Terima kasih telah menunaikan infak rutin bulan ini.
                            </div>
                        </div>
                    </div>
                @endif

        </div>

        <div class="lg:col-span-7 bg-white border border-gray-200 p-6 rounded-2xl flex flex-col justify-between gap-4">
            <div class="space-y-1">
                <h3 class="font-bold text-gray-900 text-base">Alokasi Distribusi Manfaat</h3>
                <p class="text-xs text-gray-500">Sesuai amanah ketetapan Yayasan Wakaf IKRA Padjadjaran, infak akan dialokasikan ke dalam dua pos utama:</p>
            </div>

            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                        <span>Dana Penyaluran Program Sosial & Umat</span>
                        <span class="text-[#0b6e3f]">65%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-[#0b6e3f] h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                        <span>Operasional & Pengembangan Organisasi</span>
                        <span class="text-amber-600">35%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: 35%"></div>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center text-xs font-medium text-slate-500">
                💡 Pengingat otomatis dikirim melalui Email setiap awal bulan 
            </div>
        </div>

    </div>

    <!-- 3. TABEL RIWAYAT TRANSAKSI PERSONAL (GABUNGAN REGULER & EKSTRA) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-slate-50/50">
            <h2 class="font-bold text-gray-900 text-base">Riwayat Rekapitulasi Infak Bulanan Anda</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 border-b border-gray-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="p-4">Tanggal Pembayaran</th>
                        <th class="p-4">Jenis Infak</th>
                        <th class="p-4">Nominal</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <!-- Kondisi Dinamis untuk Jenis Infak Reguler vs Ekstra -->
                            <td class="p-4">
                                <span class="font-semibold text-slate-800">
                                    @if($trx->transaction_type === 'reguler')
                                        Infak Bulanan Reguler
                                    @else
                                        Infak Ekstra / Program Khusus
                                    @endif
                                </span>
                            </td>
                            <td class="p-4 font-bold text-emerald-600">
                                + Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($trx->payment_id)
                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase">
                                        Berhasil
                                    </span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-sm text-gray-400 italic">
                                Belum ada riwayat catatan infak yang terekam pada akun Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection