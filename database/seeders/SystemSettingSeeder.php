<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cleanup old tiered settings
        \App\Models\SystemSetting::where('key', 'like', 'loan_limit_%')->delete();

        $settings = [
            // Mahasiswa Limit (Flat)
            [
                'key' => 'loan_limit_mahasiswa',
                'value' => '5',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Mahasiswa',
                'description' => 'Maksimal buku yang dapat dipinjam oleh mahasiswa',
            ],

            // Dosen Limit (Flat)
            [
                'key' => 'loan_limit_dosen',
                'value' => '10',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Dosen',
                'description' => 'Maksimal buku yang dapat dipinjam oleh dosen',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
