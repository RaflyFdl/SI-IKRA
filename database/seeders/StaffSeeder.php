<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        Staff::create([
            'nama' => 'Rafly Admin',
            'email' => 'admin@ikra.com',
            'password' => bcrypt('admin123'), // Password: admin123
            'role' => 'admin'
        ]);

        // 2. Akun Keuangan
        Staff::create([
            'nama' => 'Siti Keuangan',
            'email' => 'keuangan@ikra.com',
            'password' => bcrypt('keuangan123'), // Password: keuangan123
            'role' => 'keuangan'
        ]);

        // 3. Akun Operasional
        Staff::create([
            'nama' => 'Budi Operasional',
            'email' => 'operasional@ikra.com',
            'password' => bcrypt('operasional123'), // Password: operasional123
            'role' => 'operasional'
        ]);

        // 4. Akun Pembina
        Staff::create([
            'nama' => 'dr. H. Pembina Yayasan',
            'email' => 'pembina@ikra.com',
            'password' => bcrypt('pembina123'), // Password: pembina123
            'role' => 'pembina'
        ]);
    }
}