@extends('member.member_layout')

@section('title', 'Instruksi Pembayaran Infak')

@section('member_content')
<div class="max-w-2xl mx-auto space-y-6">

    <div id="status-payment-box">
        @if($transaction->payment_id)
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center justify-center gap-3 shadow-sm border-2">
                <div class="text-center w-full">
                    <h4 class="text-lg font-bold text-emerald-800 uppercase tracking-wide">🎉 Pembayaran Berhasil Dilakukan</h4>
                    <p class="text-xs text-emerald-600 mt-1">Terima kasih, dana infak Anda telah berhasil kami terima sistem.</p>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3 shadow-sm">
                <span class="text-xl mt-0.5">⏳</span>
                <div class="w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5">
                        <h4 class="text-sm font-bold text-amber-800 uppercase tracking-wide">Menunggu Pembayaran</h4>
                        <div class="inline-flex items-center gap-1 bg-amber-200/60 text-amber-900 px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold tracking-wider shrink-0 w-fit">
                            ⏱️ <span id="countdown-timer">02:00:00</span>
                        </div>
                    </div>
                    <p class="text-xs text-amber-700 mt-2 leading-relaxed">
                        Segera selesaikan pembayaran sebelum waktu di atas habis agar transaksi Anda tidak otomatis dibatalkan oleh sistem.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-slate-50/50">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Program Yang Dipilih</span>
            <h2 class="text-lg font-bold text-slate-800 mt-0.5">{{ $transaction->program_name }}</h2>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bank Tujuan</span>
                    <strong class="text-slate-700 text-sm block mt-1">Virtual Account {{ $transaction->bank_code }}</strong>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Metode Pembayaran</span>
                    <strong class="text-slate-700 text-sm block mt-1">Virtual Account (Closed)</strong>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Virtual Account Anda</span>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-center justify-between">
                    <strong id="va-number" class="text-xl md:text-2xl font-mono text-indigo-600 tracking-widest">{{ $transaction->account_number }}</strong>
                    <button onclick="copyInvoiceText('{{ $transaction->account_number }}', this)" class="bg-white hover:bg-indigo-600 hover:text-white text-slate-700 text-xs font-bold px-4 py-2 rounded-lg border border-slate-200 shadow-sm transition-all duration-200 focus:outline-none shrink-0 cursor-pointer">
                        Salin
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah Pembayaran</span>
                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 flex items-center justify-between">
                    <strong class="text-xl md:text-2xl font-bold text-emerald-700">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong>
                </div>
                <p class="text-[11px] text-emerald-700 font-medium leading-relaxed">
                    ✨ *Nominal di atas akan langsung muncul secara otomatis di layar ATM / M-Banking Anda setelah Anda memasukkan nomor Virtual Account di atas dengan benar.
                </p>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-gray-100">
            <a href="{{ route('member.programs.index') }}" class="w-full text-center block border border-gray-300 hover:bg-gray-100 bg-white text-slate-700 text-sm font-semibold py-3 px-4 rounded-xl transition duration-200">
                Kembali ke Program
            </a>
        </div>
    </div>

</div>

<script>
    // 1. Fungsi Salin Teks
    function copyInvoiceText(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = button.innerText;
            button.innerText = 'Tersalin!';
            button.classList.remove('text-slate-700');
            button.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
            
            setTimeout(() => {
                button.innerText = originalText;
                button.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                button.classList.add('text-slate-700');
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin text: ', err);
        });
    }

    // 2. Logic Countdown Timer
    const createdAtTime = new Date("{{ \Carbon\Carbon::parse($transaction->created_at)->toIso8601String() }}").getTime();
    const expiryTime = createdAtTime + (2 * 60 * 60 * 1000);

    const timerDisplay = document.getElementById('countdown-timer');

    if (timerDisplay) {
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiryTime - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                timerDisplay.innerHTML = "EXPIRED";
                timerDisplay.classList.remove('bg-amber-200/60', 'text-amber-900');
                timerDisplay.classList.add('bg-rose-100', 'text-rose-700');
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const formattedHours = String(hours).padStart(2, '0');
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');

            timerDisplay.innerHTML = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
        }, 1000);
    }

    // 🚀 3. Logic Real-time Check Status (AJAX Polling setiap 3 detik)
    @if(!$transaction->payment_id)
        const statusInterval = setInterval(() => {
            fetch("{{ route('member.extra.check-status', $transaction->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'lunas') {
                        clearInterval(statusInterval);
                        
                        document.getElementById('status-payment-box').innerHTML = `
                            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center justify-center gap-3 shadow-sm border-2">
                                <div class="text-center w-full">
                                    <h4 class="text-lg font-bold text-emerald-800 uppercase tracking-wide">🎉 Pembayaran Berhasil Dilakukan</h4>
                                    <p class="text-xs text-emerald-600 mt-1">Terima kasih, dana infak Anda telah berhasil kami terima sistem.</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(err => console.error('Error polling status:', err));
        }, 3000);
    @endif {{-- 👈 SUDAH DITUTUP DISINI AMAN SEKARANG --}}
</script>
@endsection