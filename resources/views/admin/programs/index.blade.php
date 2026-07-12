<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA System - Publikasi Program</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f4f6f9] min-h-screen flex text-slate-800 font-sans">

    <aside class="w-64 bg-[#111827] text-slate-300 flex flex-col min-h-screen sticky top-0 shadow-xl">
        <div class="p-5 flex items-center gap-3 border-b border-slate-800">
            <div class="bg-emerald-500 text-white font-bold p-2 rounded-xl text-center min-w-[40px]">IK</div>
            <div>
                <h1 class="font-bold text-white leading-tight">IKRA System</h1>
                <p class="text-xs text-slate-400">Admin</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-2 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-user-check text-lg"></i>
                <span>Verifikasi Pendaftar</span>
            </a>
            <a href="{{ route('admin.programs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-600 text-white transition">
                <i class="fa-solid fa-bullhorn text-lg"></i>
                <span>Publikasi Program</span>
            </a>
            
            <div class="pt-4 pb-1 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Data Master (CRUD)</div>
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-users-gear text-lg"></i>
                <span>Master Anggota</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-folder-tree text-lg"></i>
                <span>Master Program Ekstra</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-800 text-xs text-slate-500 text-center">
            &copy; 2026 IKRA Padjadjaran
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">📢 Publikasi Program Infak Ekstra</h2>
                <p class="text-sm text-slate-500">Buat program baru dan generate Virtual Account secara otomatis.</p>
            </div>
            <a href="{{ route('register') }}" class="border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-sm" target="_blank">
                Buka Form Daftar ↗
            </a>
        </header>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm h-fit">
                <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-square-plus text-emerald-600"></i> Input Program Baru
                </h2>
                <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Kategori Program</label>
                            <select id="categorySelect" name="category" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800 bg-white cursor-pointer">
                                <option value="Donasi Umum">Donasi Umum</option>
                                <option value="Podcast">Podcast (Operasional)</option>
                                <option value="Cinema Edukasi">Cinema Edukasi (Operasional)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nama Program</label>
                            <input type="text" name="name" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="Contoh: Wakaf Masjid Al-Afiati">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Deskripsi Program</label>
                            <textarea name="description" rows="3" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="Detail peruntukan dana..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Foto / Sampul Program</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        </div>

                        <div id="donasiFields" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Target Pendanaan (Rp)</label>
                                <input type="number" id="targetAmountInput" name="target_amount" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="100000000">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Batas Waktu (End Date)</label>
                                <input type="date" id="endDateInput" name="end_date" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tanggal Pelaksanaan</label>
                                <input type="date" id="executionDateInput" name="execution_date" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800">
                                <p id="date_error_message" class="text-[11px] text-rose-600 font-bold mt-1 hidden">
                                    ⚠️ Jadwal bentrok! Tanggal ini sudah digunakan program lain.
                                </p>
                            </div>
                        </div>

                        <!-- Rincian Kebutuhan Dana (Dynamic Form) -->
                        <div class="border-t border-slate-100 pt-4 mt-4">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Rincian Kebutuhan Dana (Opsional)</label>
                                <button type="button" id="addRincianBtn" class="text-xs bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold px-3 py-1.5 rounded-lg transition">
                                    <i class="fa-solid fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div id="rincianContainer" class="space-y-3">
                                <!-- Baris Pertama (Kosong) -->
                                <div class="flex gap-2 items-end rincian-row">
                                    <div class="flex-1">
                                        <input type="text" name="nama_barang[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Nama Brg (Cth: Beras)">
                                    </div>
                                    <div class="w-16">
                                        <input type="number" name="jumlah[]" value="1" min="1" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Qty">
                                    </div>
                                    <div class="w-20">
                                        <input type="text" name="satuan[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Satuan">
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" name="harga[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Harga (Rp)">
                                    </div>
                                    <button type="button" class="remove-btn text-rose-500 hover:text-rose-700 p-2 hidden">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2">* Kosongkan baris jika tidak ada rincian tambahan.</p>
                        </div>

                        <button type="submit" id="submitButton" class="w-full bg-[#111827] hover:bg-slate-800 text-white font-bold text-sm py-3 rounded-xl transition shadow-md mt-2 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-key"></i> Simpan & Buat VA Otomatis
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-emerald-600"></i> Daftar Program Saat Ini
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="p-4 w-2/5">Nama Program / Kategori</th>
                                <th class="p-4 w-1/5">Target Dana</th>
                                <th class="p-4 w-1/5">Nomor VA Xendit</th>
                                <th class="p-4 w-1/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($programs as $program)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <span class="font-bold text-slate-800 block mb-1.5">{{ $program->name }}</span>
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $program->category == 'Podcast' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($program->category == 'Cinema Edukasi' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-slate-50 text-slate-600 border-slate-200') }}">
                                                    {{ $program->category ?? 'Donasi Umum' }}
                                                </span>
                                                <span class="text-xs text-slate-400 font-medium">
                                                    @if(in_array($program->category, ['Podcast', 'Cinema Edukasi']))
                                                        ♾️ Terbuka Terus
                                                    @elseif($program->end_date)
                                                        Hingga: {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                                                    @else
                                                        Batas Waktu: Terbuka Terus
                                                    @endif
                                                </span>
                                            </div>
                                            @if($program->execution_date && $program->category == 'Donasi Umum')
                                                <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                                    📅 Pelaksanaan: {{ \Carbon\Carbon::parse($program->execution_date)->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 font-semibold text-slate-600">
                                        @if(in_array($program->category, ['Podcast', 'Cinema Edukasi']))
                                            <span class="text-slate-400 italic text-xs font-normal">Tidak ada target</span>
                                        @elseif($program->target_amount)
                                            Rp {{ number_format($program->target_amount, 0, ',', '.') }}
                                        @else
                                            Tidak Terbatas
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-xs text-indigo-600 font-bold tracking-wider">
                                        {{ $program->va_number ?? 'Sedang diproses...' }}
                                    </td>
                                    <td class="p-4">
                                        @if($program->status == 'active')
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Aktif</span>
                                        @elseif($program->status == 'ready')
                                            <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Ready</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-slate-400 font-medium">Belum ada program infak ekstra yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const categorySelect = document.getElementById("categorySelect");
            const donasiFields = document.getElementById("donasiFields");
            const targetAmountInput = document.getElementById("targetAmountInput");
            const endDateInput = document.getElementById("endDateInput");
            const executionDateInput = document.getElementById("executionDateInput");
            
            const dateErrorMessage = document.getElementById("date_error_message");
            const submitButton = document.getElementById("submitButton");

            // Ambil daftar tanggal sibuk dari server
            const busyDates = @json($busyDates ?? []);

            function adjustFormFields() {
                if (categorySelect.value === "Donasi Umum") {
                    donasiFields.style.display = "block";
                    targetAmountInput.required = true;
                    endDateInput.required = true;
                    executionDateInput.required = true;
                } else {
                    donasiFields.style.display = "none";
                    targetAmountInput.required = false;
                    endDateInput.required = false;
                    executionDateInput.required = false;
                    
                    targetAmountInput.value = "";
                    endDateInput.value = "";
                    executionDateInput.value = "";
                }
                checkDateConflict();
            }

            function checkDateConflict() {
                // Hanya check jika kategori Donasi Umum dan ada tanggal yang diisi
                if (categorySelect.value === "Donasi Umum" && executionDateInput.value) {
                    const selectedDate = executionDateInput.value; // Format: YYYY-MM-DD
                    
                    if (busyDates.includes(selectedDate)) {
                        dateErrorMessage.classList.remove("hidden");
                        executionDateInput.classList.remove("border-slate-200", "focus:border-slate-900");
                        executionDateInput.classList.add("border-rose-400", "bg-rose-50/30", "focus:border-rose-500");
                        
                        submitButton.disabled = true;
                        submitButton.classList.remove("bg-[#111827]", "hover:bg-slate-800", "cursor-pointer");
                        submitButton.classList.add("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                    } else {
                        clearConflict();
                    }
                } else {
                    clearConflict();
                }
            }

            function clearConflict() {
                dateErrorMessage.classList.add("hidden");
                executionDateInput.classList.remove("border-rose-400", "bg-rose-50/30", "focus:border-rose-500");
                executionDateInput.classList.add("border-slate-200", "focus:border-slate-900");
                
                submitButton.disabled = false;
                submitButton.classList.remove("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                submitButton.classList.add("bg-[#111827]", "hover:bg-slate-800", "cursor-pointer");
            }

            categorySelect.addEventListener("change", adjustFormFields);
            executionDateInput.addEventListener("input", checkDateConflict);
            adjustFormFields();

            // Script untuk Dynamic Form Rincian Kebutuhan Dana
            const addRincianBtn = document.getElementById("addRincianBtn");
            const rincianContainer = document.getElementById("rincianContainer");

            addRincianBtn.addEventListener("click", function() {
                const row = document.createElement("div");
                row.className = "flex gap-2 items-end rincian-row mt-3";
                row.innerHTML = `
                    <div class="flex-1">
                        <input type="text" name="nama_barang[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Nama Brg (Cth: Beras)">
                    </div>
                    <div class="w-16">
                        <input type="number" name="jumlah[]" value="1" min="1" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Qty">
                    </div>
                    <div class="w-20">
                        <input type="text" name="satuan[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Satuan (kg)">
                    </div>
                    <div class="flex-1">
                        <input type="number" name="harga[]" class="w-full border border-slate-200 rounded-xl px-2 py-2 text-xs focus:outline-none focus:border-slate-900" placeholder="Harga Total (Rp)">
                    </div>
                    <button type="button" class="remove-btn text-rose-500 hover:text-rose-700 p-2">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;
                rincianContainer.appendChild(row);
                
                // Aktifkan tombol remove
                row.querySelector(".remove-btn").addEventListener("click", function() {
                    row.remove();
                });
            });

            // Handle remove untuk row yang pertama jika dimunculkan (opsional)
        });
    </script>
</body>
</html>