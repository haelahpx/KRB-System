<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WifiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data diambil dari krbs_db.sql
        // Note: remove explicit primary key values so auto-increment won't conflict.
        // Use upsert to avoid duplicate-key errors in production (update if exists).
        $wifis = [
            [
                'company_id' => 2, // Kebun Raya Bogor
                'ssid' => 'KRB_GUEST_FREE',
                'password' => 'BogorSejuk2025',
                'location' => 'Lobby Utama & Loket',
                'is_active' => 1,
                'created_at' => '2025-11-22 12:18:23',
                'updated_at' => '2025-11-22 12:18:23',
            ],
            [
                'company_id' => 2, // Kebun Raya Bogor
                'ssid' => 'KRB_STAFF_ONLY',
                'password' => 'Staff@Bogor123',
                'location' => 'Ruang Office Lt. 2',
                'is_active' => 1,
                'created_at' => '2025-11-22 12:18:23',
                'updated_at' => '2025-11-22 12:18:23',
            ],
            [
                'company_id' => 3, // Kebun Raya Bali
                'ssid' => 'BALI_VISITOR',
                'password' => 'BaliExotic',
                'location' => 'Area Restaurant',
                'is_active' => 1,
                'created_at' => '2025-11-22 12:18:23',
                'updated_at' => '2025-11-22 12:18:23',
            ],
            [
                'company_id' => 4, // Kebun Raya Cibodas
                'ssid' => 'CIBODAS_ADMIN',
                'password' => 'Cibodas#99',
                'location' => 'Ruang Server',
                'is_active' => 1,
                'created_at' => '2025-11-22 12:18:23',
                'updated_at' => '2025-11-22 12:18:23',
            ],
        ];

        // Upsert by `company_id`+`ssid` to insert new rows or update existing ones safely.
        DB::table('wifis')->upsert(
            $wifis,
            ['company_id', 'ssid'],
            ['password', 'location', 'is_active', 'updated_at']
        );
    }
}