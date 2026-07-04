<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengajuan Operasional - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="max-w-3xl mx-auto py-12 px-4">
        <div class="mb-6">
            <a href="{{ route('operational.operasional.index') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Riwayat
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 bg-slate-900 text-white">
                <h1 class="text-xl font-bold">Form Pengajuan Dana Operasional Internal</h1>
                <p class="text-xs text-slate-400 mt-1">Ajukan multi-kebutuhan operasional bulanan atau mingguan untuk mendapatkan persetujuan Pembina.</p>
            </div>

            <form action="{{ route('operational.operasional.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Judul / Keterangan Pengajuan</label>
                    <input type="text" name="title" placeholder="Contoh: Kebutuhan Operasional Kantor Bulan Juli 2026" class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" required>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Rincian Item Kebutuhan (Multi-Item)</label>
                        <button type="button" id="add-item-btn" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-plus-circle"></i> Tambah Kebutuhan
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        <div class="flex gap-3 items-center item-row">
                            <div class="flex-1">
                                <input type="text" name="descriptions[]" placeholder="Uraian kebutuhan (misal: Tagihan Listrik)" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" required>
                            </div>
                            <div class="w-48">
                                <input type="number" name="amounts[]" placeholder="Nominal (Rp)" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 amount-input" required>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-red-500 remove-item-btn cursor-pointer invisible">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 flex justify-between items-center">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Anggaran Diajukan</span>
                    <span id="total-display" class="text-lg font-black text-emerald-700">Rp 0</span>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('operational.operasional.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i> Kirim ke Pembina
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Logika Menambahkan Baris Form Baru
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const newRow = document.createElement('div');
            newRow.className = 'flex gap-3 items-center item-row';
            newRow.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="descriptions[]" placeholder="Uraian kebutuhan" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" required>
                </div>
                <div class="w-48">
                    <input type="number" name="amounts[]" placeholder="Nominal (Rp)" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500 amount-input" required>
                </div>
                <button type="button" class="text-slate-400 hover:text-red-500 remove-item-btn cursor-pointer">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            container.appendChild(newRow);
            attachEvents(); // Daftarkan event oninput & onclick untuk baris baru
        });

        // Mendaftarkan fungsi hapus & hitung otomatis ke elemen input yang aktif
        function attachEvents() {
            // Logika Menghapus Baris Kebutuhan
            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.onclick = function() {
                    this.parentElement.remove();
                    calculateTotal(); // Hitung ulang setelah baris dihapus
                };
            });

            // Logika Menghitung Otomatis ketika user sedang mengetik angka nominal
            document.querySelectorAll('.amount-input').forEach(input => {
                input.oninput = calculateTotal;
            });
        }

        // Fungsi Menghitung Total Akumulasi
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.amount-input').forEach(input => {
                const val = parseFloat(input.value) || 0;
                total += val;
            });
            // Tampilkan dengan format mata uang rupiah lokal (IDR)
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Jalankan event binding saat halaman pertama kali dimuat
        attachEvents();
    </script>
</body>
</html>