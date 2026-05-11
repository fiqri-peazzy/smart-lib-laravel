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
        // Cleanup old dosen-related tiered settings
        \App\Models\SystemSetting::where('key', 'like', 'loan_limit_dosen_%')->delete();

        $settings = [
            // Mahasiswa Limits
            [
                'key' => 'loan_limit_mahasiswa_90',
                'value' => '5',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Mahasiswa (Score 90+)',
                'description' => 'Maksimal buku untuk mahasiswa dengan credit score >= 90',
            ],
            [
                'key' => 'loan_limit_mahasiswa_70',
                'value' => '4',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Mahasiswa (Score 70+)',
                'description' => 'Maksimal buku untuk mahasiswa dengan credit score >= 70',
            ],
            [
                'key' => 'loan_limit_mahasiswa_50',
                'value' => '3',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Mahasiswa (Score 50+)',
                'description' => 'Maksimal buku untuk mahasiswa dengan credit score >= 50',
            ],
            [
                'key' => 'loan_limit_mahasiswa_default',
                'value' => '2',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Mahasiswa (Default)',
                'description' => 'Maksimal buku untuk mahasiswa dengan credit score < 50',
            ],

            // Dosen Limits (Flat)
            [
                'key' => 'loan_limit_dosen',
                'value' => '25',
                'type' => 'integer',
                'group' => 'loan_limits',
                'display_name' => 'Batas Pinjam Dosen (Flat)',
                'description' => 'Maksimal buku yang dapat dipinjam oleh dosen (tidak terpengaruh credit score)',
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
