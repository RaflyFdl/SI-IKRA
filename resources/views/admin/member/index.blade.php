<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKRA System - Data Master Anggota</title>
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
                <i class="fa-solid stroke-2 fa-user-check text-lg"></i>
                <span>Verifikasi Pendaftar</span>
            </a>
            <a href="{{ route('admin.programs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-bullhorn text-lg"></i>
                <span>Publikasi Program</span>
            </a>
            
            <div class="pt-4 pb-1 px-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Data Master (CRUD)</div>
            
            <a href="{{ route('admin.member.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-600 text-white transition">
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
                <h2 class="text-2xl font-bold text-slate-900">👥 Data Master Anggota</h2>
                <p class="text-sm text-slate-500">Menu khusus Admin untuk mengelola semua database jemaah/anggota IKRA Padjadjaran</p>
            </div>
            <button onclick="toggleModal('addMemberModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> + Tambah Anggota Manual
            </button>
        </header>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm shadow-sm">
                <p class="font-bold flex items-center gap-2 mb-2"><i class="fa-solid fa-circle-xmark"></i> Terdapat kesalahan pada form:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#111827] text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800">
                            <th class="p-4 w-16 text-center">No</th>
                            <th class="p-4">Nama Lengkap</th>
                            <th class="p-4">Angkatan</th>
                            <th class="p-4">No. WhatsApp</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">VA Muamalat</th>
                            <th class="p-4">Status Akun</th>
                            <th class="p-4 text-center">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($members as $index => $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $item->nama }}</div>
                                <span class="text-xs text-slate-400 font-mono">ID Member: #{{ $item->id }}</span>
                            </td>
                            <td class="p-4">
                                <span class="inline-block bg-slate-100 text-slate-700 font-semibold text-xs px-2.5 py-1 rounded-lg border border-slate-200">{{ $item->angkatan }}</span>
                            </td>
                            <td class="p-4 font-medium text-slate-600">{{ $item->no_wa }}</td>
                            <td class="p-4 text-slate-600 font-mono text-xs">{{ $item->email }}</td>
                            <td class="p-4">
                                @if($item->va_muamalat)
                                    <span class="bg-slate-50 text-slate-700 border border-slate-200 text-xs font-mono font-bold px-2.5 py-1.5 rounded-lg tracking-wide">
                                        {{ $item->va_muamalat }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">Belum dibuat</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if(strtolower($item->status) == 'active')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-lg">Active</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold px-2.5 py-1 rounded-lg">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-1.5">
                                    <button onclick="toggleModal('detailModal{{ $item->id }}')" class="border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 font-bold text-xs px-2.5 py-1.5 rounded-xl transition shadow-sm">
                                        Detail
                                    </button>
                                    <button onclick="toggleModal('editModal{{ $item->id }}')" class="border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100 font-bold text-xs px-2.5 py-1.5 rounded-xl transition shadow-sm">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.member.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data master anggota ini secara permanen?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 font-bold text-xs px-2.5 py-1.5 rounded-xl transition shadow-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="detailModal{{ $item->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
                            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden text-left">
                                <div class="bg-slate-900 p-4 text-white flex justify-between items-center">
                                    <h5 class="font-bold">📄 Profil Ringkas Member</h5>
                                    <button onclick="toggleModal('detailModal{{ $item->id }}')" class="text-white/80 hover:text-white text-xl">&times;</button>
                                </div>
                                <div class="p-6 space-y-3 text-sm">
                                    <div><span class="text-xs uppercase font-bold text-slate-400">Nama Lengkap</span> <p class="text-base font-semibold text-slate-800">{{ $item->nama }}</p></div>
                                    <hr class="border-slate-100">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div><span class="text-xs uppercase font-bold text-slate-400">ID Anggota</span> <p class="font-mono text-slate-700">#{{ $item->id }}</p></div>
                                        <div><span class="text-xs uppercase font-bold text-slate-400">Angkatan</span> <p class="text-slate-700">{{ $item->angkatan }}</p></div>
                                    </div>
                                    <hr class="border-slate-100">
                                    <div><span class="text-xs uppercase font-bold text-slate-400">No. WhatsApp</span> <p class="text-slate-700">{{ $item->no_wa }}</p></div>
                                    <hr class="border-slate-100">
                                    <div><span class="text-xs uppercase font-bold text-slate-400">Alamat Email</span> <p class="text-slate-700 font-mono">{{ $item->email }}</p></div>
                                    <hr class="border-slate-100">
                                    <div><span class="text-xs uppercase font-bold text-slate-400">Virtual Account Muamalat</span> <p class="font-mono text-emerald-600 font-bold text-base tracking-wide">{{ $item->va_muamalat ?? 'Belum terbuat' }}</p></div>
                                </div>
                                <div class="p-4 bg-slate-50 border-t border-slate-100 text-right">
                                    <button onclick="toggleModal('detailModal{{ $item->id }}')" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-200 hover:bg-slate-300 rounded-xl transition">Tutup</button>
                                </div>
                            </div>
                        </div>

                        <div id="editModal{{ $item->id }}" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
                            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden text-left">
                                <div class="bg-amber-600 p-4 text-white flex justify-between items-center">
                                    <h5 class="font-bold">✏️ Edit Data Master Anggota</h5>
                                    <button onclick="toggleModal('editModal{{ $item->id }}')" class="text-white/80 hover:text-white text-xl">&times;</button>
                                </div>
                                <form action="{{ route('admin.member.update', $item->id) }}" method="POST" class="p-6 space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap:</label>
                                        <input type="text" name="nama" value="{{ $item->nama }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Angkatan:</label>
                                            <input type="number" name="angkatan" value="{{ $item->angkatan }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Status Akun:</label>
                                            <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                                                <option value="active" {{ strtolower($item->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="pending" {{ strtolower($item->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="rejected" {{ strtolower($item->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No. WhatsApp:</label>
                                        <input type="text" name="no_wa" value="{{ $item->no_wa }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat Email:</label>
                                        <input type="email" name="email" value="{{ $item->email }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor VA Muamalat:</label>
                                        <input type="text" name="va_muamalat" value="{{ $item->va_muamalat }}" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800 font-mono">
                                    </div>
                                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                        <button type="button" onclick="toggleModal('editModal{{ $item->id }}')" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition shadow-md">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400 font-medium">
                                <i class="fa-solid fa-users text-2xl mb-2 text-slate-300 block"></i>
                                Belum ada data master anggota di database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="addMemberModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden text-left">
            <div class="bg-emerald-600 p-4 text-white flex justify-between items-center">
                <h5 class="font-bold">➕ Tambah Anggota Baru (Manual)</h5>
                <button onclick="toggleModal('addMemberModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
            </div>
            <form action="{{ route('admin.member.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap:</label>
                    <input type="text" name="nama" placeholder="Masukkan nama lengkap jemaah" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Angkatan:</label>
                    <input type="number" name="angkatan" placeholder="Contoh: 2023" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No. WhatsApp:</label>
                    <input type="text" name="no_wa" placeholder="Contoh: 08123456789" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat Email:</label>
                    <input type="email" name="email" placeholder="contoh@domain.com" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bukti Pendukung <span class="text-slate-400 normal-case font-normal">(opsional)</span>:</label>
                    <input type="file" name="bukti_pendukung" accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                    <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, atau PDF. Boleh dikosongkan.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password Akun:</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-slate-900 text-slate-800" required>
                    <p class="text-xs text-slate-400 mt-1">Password ini akan digunakan anggota untuk login.</p>
                </div>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="toggleModal('addMemberModal')" class="px-4 py-2 text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition shadow-md">Simpan Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>