@extends('member.member_layout')

@section('title', 'Pengaturan Profil')

@section('member_content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- KARTU INFORMASI UTAMA PROFIL -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        
        <!-- Header Kartu -->
        <div class="p-6 border-b border-gray-100 bg-slate-50/50 flex items-center space-x-4">
            <div class="w-14 h-14 bg-emerald-100 text-[#0b6e3f] rounded-full flex items-center justify-center text-xl font-bold border border-emerald-200">
                {{ strtoupper(substr($member->nama ?? 'A', 0, 1)) }}
            </div>
            <div>
                <h2 class="font-bold text-gray-900 text-lg">{{ $member->nama }}</h2>
                <p class="text-xs text-gray-500">Anggota Resmi Yayasan IKRA Padjadjaran</p>
            </div>
        </div>

        <!-- Detail Data Anggota -->
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Baris: Angkatan -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Angkatan</label>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-gray-100 text-sm font-semibold text-gray-800">
                        {{ $member->angkatan }}
                    </div>
                </div>

                <!-- Baris: Status Akun -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status Keanggotaan</label>
                    <div class="bg-emerald-50/60 p-3.5 rounded-xl border border-emerald-100 text-sm font-bold text-emerald-700 uppercase">
                        ● {{ $member->status }}
                    </div>
                </div>

                <!-- Baris: Nomor WhatsApp -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">No. WhatsApp</label>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-gray-100 text-sm font-mono font-semibold text-gray-800">
                        {{ $member->no_wa }}
                    </div>
                </div>

                <!-- Baris: Email Terdaftar -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</label>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-gray-100 text-sm font-mono font-semibold text-gray-700 break-all">
                        {{ $member->email }}
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection