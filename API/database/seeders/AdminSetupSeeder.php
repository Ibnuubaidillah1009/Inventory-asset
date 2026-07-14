<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSetupSeeder extends Seeder
{
    /**
     * Seeder untuk setup akun admin default.
     *
     * Catatan: Peran 'Admin' dan hak akses-nya sudah dibuat oleh
     * AksesSeeder + PeranAksesSeeder. Seeder ini hanya membuat
     * akun pengguna admin jika belum ada.
     */
    public function run(): void
    {
        // Ambil ID peran Admin
        $peranAdmin = DB::table('peran')->where('nama_peran', 'Admin')->first();

        if (!$peranAdmin) {
            $this->command->error('❌ Peran "Admin" belum ada. Jalankan PeranAksesSeeder terlebih dahulu.');
            return;
        }

        // Buat akun admin jika belum ada
        $admin = DB::table('pengguna')->where('username', 'admin')->first();

        if (!$admin) {
            DB::table('pengguna')->insert([
                'username' => 'admin',
                'password' => Hash::make('password'),
                'id_peran' => $peranAdmin->id_peran,
            ]);
            $this->command->info('✅ Akun admin berhasil dibuat (username: admin, password: password)');
        } else {
            // Update peran ke Admin jika akun sudah ada
            DB::table('pengguna')
                ->where('username', 'admin')
                ->update(['id_peran' => $peranAdmin->id_peran]);
            $this->command->info('ℹ️  Akun admin sudah ada, peran diperbarui ke Admin.');
        }
    }
}