<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Dana Operasional - Tim Operasional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white shrink-0 p-5 space-y-6">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-emerald-600 text-white font-bold p-2 rounded-xl text-center tracking-wider text-sm">IK</div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight">IKRA System</h1>
                    <p class="text-[10px] text-slate-400">Tim Operasional</p>
                </div>
            </div>
            
            <nav class="space-y-1">
                <a href="/operasional/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 text-white shadow-md transition-all">
                    <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i> Ajukan Operasional
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fa-solid fa-money-bill-trend-up w-5 text-center"></i> Penyaluran Infak Reguler
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-10 space-y-8">
            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Form Pengajuan Dana Operasional</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ajukan anggaran kebutuhan internal kantor atau logistik lapangan ke Pembina & Keuangan</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 max-w-3xl">
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Judul Pengajuan / Kegiatan</label>
                        <input type="text" name="title" required placeholder="Contoh: Belanja Bulanan ATK & Token Listrik Kantor" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 tracking-wider mb-2">Komponen Detail & Anggaran (Gunakan baris baru untuk item berbeda)</label>
                        <div class="space-y-3" id="wrapper-item">
                            <div class="flex gap-3 item-row">
                                <input type="text" name="descriptions[]" required placeholder="Deskripsi komponen (e.g., Kertas A4 2 Rim)" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500">
                                <input type="number" name="amounts[]" required placeholder="Nominal Rp" class="w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>
                        <button type="button" id="add-item-btn" class="mt-3 text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                            <i class="fa-solid fa-circle-plus"></i> Tambah Item Kebutuhan
                        </button>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl transition-all shadow-sm">
                            Kirim Pengajuan ke Pembina
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Script sederhana untuk duplikasi input item komponen dinamis
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const wrapper = document.getElementById('wrapper-item');
            const newRow = document.createElement('div');
            newRow.className = 'flex gap-3 item-row mt-2';
            newRow.innerHTML = `
                <input type="text" name="descriptions[]" required placeholder="Deskripsi komponen" class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500">
                <input type="number" name="amounts[]" required placeholder="Nominal Rp" class="w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500">
            `;
            wrapper.appendChild(newRow);
        });
    </script>
</body>
</html>