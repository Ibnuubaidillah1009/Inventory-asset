<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder yang benar untuk RBAC system:
     *
     * 1. AksesSeeder       → Buat struktur hierarchical menu (parent + child)
     * 2. PeranAksesSeeder  → Buat peran & assign hak akses per peran
     * 3. AdminSetupSeeder  → Buat akun admin default
     * 4. DummyDataSeeder   → Data dummy (opsional, untuk development)
     */
    public function run(): void
    {
        $this->call([
            AksesSeeder::class,
            PeranAksesSeeder::class,
            AdminSetupSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}