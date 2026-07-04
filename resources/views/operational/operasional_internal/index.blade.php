<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyaluran Operasional Internal - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 p-3 rounded-lg transition">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 p-3 rounded-lg transition">
                        <i class="fa-solid fa-heart-circle-check w-5 text-center"></i>
                        <span>Penyaluran Infak Reguler</span>
                    </a>
                    <a href="{{ route('operational.operasional.index') }}" class="flex items-center space-x-3 bg-emerald-600 text-white font-medium p-3 rounded-lg transition">
                        <i class="fa-solid fa-building-user w-5 text-center"></i>
                        <span>Penyaluran Operasional</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="flex-1 p-10">
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Penyaluran Dana Operasional</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola dan pantau pengajuan dana kebutuhan internal kesekretariatan yayasan.</p>
                </div>
                <a href="{{ route('operational.operasional.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-plus"></i> Ajukan Operasional Baru
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium mb-6">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium mb-6">
                    ❌ {{ session('error') ?? 'Terjadi kesalahan pada data yang Anda masukkan.' }}
                    @if($errors->any())
                        <ul class="list-disc pl-5 mt-2 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-bold text-slate-800">Riwayat Pengajuan Internal</h2>
                </div>

                <div class="p-6">
                    @if($requests->isEmpty())
                        <div class="text-center py-12 text-slate-400 text-sm">
                            <span class="text-3xl block mb-2">🏢</span>
                            Belum ada pengajuan operasional internal yang terdata.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($requests as $req)
                                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm space-y-4">
                                    <div class="flex flex-wrap justify-between items-start gap-4">
                                        <div>
                                            <h3 class="text-base font-bold text-slate-800">{{ $req->title }}</h3>
                                            <p class="text-xs text-slate-400 mt-0.5">Diajukan pada: {{ $req->created_at->translatedFormat('d F Y - H:i') }} WIB</p>
                                        </div>
                                        <div class="flex gap-2">
                                            @if($req->status_pembina === 'pending')
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">⏳ Menunggu Pembina</span>
                                            @elseif($req->status_pembina === 'approved_pembina')
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 uppercase border border-emerald-200">✅ Disetujui Pembina</span>
                                            @else
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-rose-50 text-rose-700 uppercase border border-rose-200">❌ Ditolak Pembina</span>
                                            @endif

                                            @if($req->status_keuangan === 'pending' && $req->status_pembina === 'approved_pembina')
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">⏳ Menunggu Pencairan Kas</span>
                                            @elseif($req->status_keuangan === 'dicairkan')
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-blue-50 text-blue-700 uppercase border border-blue-200">💸 Dana Dicairkan</span>
                                            @elseif($req->status_keuangan === 'dilaporkan')
                                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-purple-50 text-purple-700 uppercase border border-purple-200">🏁 Selesai & Dilaporkan</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                        <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-2">Rincian Kebutuhan:</span>
                                        <ul class="space-y-1.5 text-xs text-slate-600">
                                            @foreach($req->items as $index => $item)
                                                <li class="flex justify-between border-b border-slate-200/60 pb-1 last:border-0 last:pb-0">
                                                    <span>{{ $index + 1 }}. {{ $item->description }}</span>
                                                    <span class="font-bold text-slate-700">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="flex justify-between items-center mt-3 pt-2.5 border-t border-slate-300">
                                            <span class="text-xs font-bold text-slate-700">Total Akumulasi</span>
                                            <span class="text-sm font-black text-emerald-600">Rp {{ number_format($req->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    @if($req->status_keuangan === 'dicairkan')
                                        <div class="mt-4 border-t border-slate-100 pt-4 bg-slate-50/40 p-5 rounded-xl border border-slate-200 space-y-4">
                                            <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-xs">
                                                <div>
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Dana Modal Awal</span>
                                                    <span class="text-base font-black text-emerald-600 block">Rp {{ number_format($req->total_amount, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Total Pengeluaran Nota</span>
                                                    <span class="text-base font-black text-slate-800 block" id="text-total-nota-{{ $req->id }}">Rp 0</span>
                                                </div>
                                            </div>
                                            
                                            <form action="{{ route('operational.operasional.laporkan', $req->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                @csrf
                                                
                                                <div class="overflow-x-auto bg-white p-3 rounded-lg border border-slate-100">
                                                    <table class="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                                                <th class="pb-2 w-32">Tanggal Nota</th>
                                                                <th class="pb-2">Uraian Belanja</th>
                                                                <th class="pb-2 w-40">Nominal (Rp)</th>
                                                                <th class="pb-2 w-10 text-center">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="container-nota-{{ $req->id }}" data-modal-awal="{{ $req->total_amount }}" class="container-nota-parent">
                                                            <tr class="border-b border-slate-100 item-nota">
                                                                <td class="py-2 pr-2">
                                                                    <input type="date" name="nota_date[]" required class="w-full text-xs p-1.5 rounded-md border border-slate-200">
                                                                </td>
                                                                <td class="py-2 pr-2">
                                                                    <input type="text" name="nota_description[]" required placeholder="Contoh: Beli Token Listrik" class="w-full text-xs p-1.5 rounded-md border border-slate-200">
                                                                </td>
                                                                <td class="py-2 pr-2">
                                                                    <input type="number" name="nota_amount[]" required oninput="hitungSistemBalansi({{ $req->id }}, {{ $req->total_amount }})" placeholder="0" class="w-full text-xs p-1.5 rounded-md border border-slate-200 input-nominal-nota">
                                                                </td>
                                                                <td class="py-2 text-center">
                                                                    <button type="button" onclick="hapusBarisNota(this, {{ $req->id }}, {{ $req->total_amount }})" class="text-rose-500 text-xs cursor-pointer"><i class="fa-solid fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    
                                                    <button type="button" onclick="tambahBarisNota({{ $req->id }}, {{ $req->total_amount }})" class="mt-3 text-[11px] font-bold text-emerald-600 flex items-center gap-1 cursor-pointer hover:text-emerald-700">
                                                        <i class="fa-solid fa-plus-circle"></i> Tambah Item Belanja
                                                    </button>
                                                </div>

                                                <div id="box-status-{{ $req->id }}" class="p-3 rounded-xl border text-xs font-medium bg-slate-50 border-slate-200 text-slate-600 flex justify-between items-center">
                                                    <span>Status & Balansi Kas Lapangan:</span>
                                                    <span id="text-status-balansi-{{ $req->id }}" class="font-bold">Pas / Seimbang</span>
                                                </div>

                                                <div id="section-refund-{{ $req->id }}" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
                                                    <label class="text-[10px] font-bold text-amber-900 uppercase block">⚠️ Wajib Unggah Bukti Transfer Sisa Saldo (Refund)</label>
                                                    <input type="file" name="refund_proof" id="input-refund-{{ $req->id }}" accept="image/*,.pdf" class="w-full text-xs file:text-[11px] file:font-semibold file:bg-amber-100 file:text-amber-800 file:border-0 file:p-1 file:px-2 file:rounded-md" />
                                                </div>

                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Catatan Evaluasi Lapangan</label>
                                                    <textarea name="realization_report" rows="2" class="w-full text-xs p-2 rounded-lg border border-slate-200" placeholder="Tulis catatan atau kendala jika ada..."></textarea>
                                                </div>

                                                <div class="flex justify-end">
                                                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold py-1.5 px-4 rounded-lg shadow-sm transition cursor-pointer">
                                                        Kirim Laporan Nota
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    @if($req->status_keuangan === 'dilaporkan')
                                        <div class="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-600 space-y-1">
                                            <div class="font-bold text-slate-700 flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check text-indigo-500"></i> Laporan Realisasi Telah Terkirim
                                            </div>
                                            <p><span class="font-medium text-slate-400">Catatan:</span> {{ $req->realization_report }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // PENTING: Memicu fungsi hitung kalkulasi otomatis pada saat pertama kali halaman selesai dimuat
        window.onload = function() {
            const containers = document.querySelectorAll('.container-nota-parent');
            containers.forEach(container => {
                const id = container.id.replace('container-nota-', '');
                const modalAwal = parseFloat(container.getAttribute('data-modal-awal')) || 0;
                hitungSistemBalansi(id, modalAwal);
            });
        };

        function tambahBarisNota(id, totalAmount) {
            const tbody = document.getElementById(`container-nota-${id}`);
            const row = document.createElement('tr');
            row.className = 'border-b border-slate-100 item-nota';
            row.innerHTML = `
                <td class="py-2 pr-2"><input type="date" name="nota_date[]" required class="w-full text-xs p-1.5 rounded-md border border-slate-200"></td>
                <td class="py-2 pr-2"><input type="text" name="nota_description[]" required placeholder="Uraian belanja" class="w-full text-xs p-1.5 rounded-md border border-slate-200"></td>
                <td class="py-2 pr-2"><input type="number" name="nota_amount[]" required oninput="hitungSistemBalansi(${id}, ${totalAmount})" placeholder="0" class="w-full text-xs p-1.5 rounded-md border border-slate-200 input-nominal-nota"></td>
                <td class="py-2 text-center"><button type="button" onclick="hapusBarisNota(this, ${id}, ${totalAmount})" class="text-rose-500 text-xs cursor-pointer"><i class="fa-solid fa-trash"></i></button></td>
            `;
            tbody.appendChild(row);
            hitungSistemBalansi(id, totalAmount);
        }

        function hapusBarisNota(btn, id, totalAmount) {
            btn.closest('tr').remove();
            hitungSistemBalansi(id, totalAmount);
        }

        function hitungSistemBalansi(id, modalAwal) {
            const container = document.getElementById(`container-nota-${id}`);
            if (!container) return;

            const inputs = container.querySelectorAll('.input-nominal-nota');
            let totalNota = 0;
            
            inputs.forEach(input => {
                totalNota += parseFloat(input.value) || 0;
            });

            const textTotal = document.getElementById(`text-total-nota-${id}`);
            if (textTotal) {
                textTotal.innerText = 'Rp ' + totalNota.toLocaleString('id-ID');
            }

            const selisih = modalAwal - totalNota;
            const boxStatus = document.getElementById(`box-status-${id}`);
            const textStatus = document.getElementById(`text-status-balansi-${id}`);
            const sectionRefund = document.getElementById(`section-refund-${id}`);
            const inputRefund = document.getElementById(`input-refund-${id}`);

            if (!boxStatus || !textStatus) return;

            if (selisih === 0) {
                boxStatus.className = "p-3 rounded-xl border text-xs font-medium bg-slate-50 border-slate-200 text-slate-700 flex justify-between items-center";
                textStatus.innerText = "Pas / Seimbang";
                if (sectionRefund) sectionRefund.classList.add('hidden');
                if (inputRefund) inputRefund.removeAttribute('required');
            } else if (selisih < 0) {
                boxStatus.className = "p-3 rounded-xl border text-xs font-medium bg-rose-50 border-rose-200 text-rose-700 flex justify-between items-center";
                textStatus.innerText = "Kurang Dana (Reimburse): Rp " + Math.abs(selisih).toLocaleString('id-ID');
                if (sectionRefund) sectionRefund.classList.add('hidden');
                if (inputRefund) inputRefund.removeAttribute('required');
            } else {
                boxStatus.className = "p-3 rounded-xl border text-xs font-medium bg-amber-50 border-amber-200 text-amber-700 flex justify-between items-center";
                textStatus.innerText = "Kelebihan Dana (Wajib Refund): Rp " + selisih.toLocaleString('id-ID');
                
                // JIKA INPUT NOMINAL MASIH NOL (Awal muat halaman), SEBISA MUNGKIN JANGAN MENG-REQUIRED-KAN INPUT HIDDEN
                if (totalNota === 0) {
                    if (sectionRefund) sectionRefund.classList.add('hidden');
                    if (inputRefund) inputRefund.removeAttribute('required');
                } else {
                    if (sectionRefund) sectionRefund.classList.remove('hidden');
                    if (inputRefund) inputRefund.setAttribute('required', 'required');
                }
            }
        }
    </script>

</body>
</html>