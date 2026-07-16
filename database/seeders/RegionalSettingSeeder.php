<?php

namespace Database\Seeders;

use App\Domain\Configuration\Models\RegionalSetting;
use Illuminate\Database\Seeder;

class RegionalSettingSeeder extends Seeder
{
    public function run(): void
    {
        RegionalSetting::query()->firstOrCreate(
            ['government_name' => 'Kabupaten Contoh'],
            [
                'app_name' => 'GCMS Kabupaten Contoh',
                'theme_color' => '#0B5ED7',
                'address' => 'Jl. Merdeka No. 1, Ibu Kota Kabupaten Contoh',
                'phone' => '021-12345678',
                'email' => 'pengaduan@kabupatencontoh.go.id',
                'website' => 'https://kabupatencontoh.go.id',
                'head_of_region' => 'H. Contoh Bupati, S.H., M.Si.',
                'regional_secretary' => 'Drs. Sekretaris Daerah Contoh, M.AP.',
                'dashboard_greeting' => 'Selamat datang di Sistem Pengaduan Masyarakat Kabupaten Contoh.',
                'social_media' => [
                    'facebook' => 'https://facebook.com/kabupatencontoh',
                    'instagram' => 'https://instagram.com/kabupatencontoh',
                    'twitter' => 'https://x.com/kabupatencontoh',
                    'youtube' => 'https://youtube.com/@kabupatencontoh',
                ],
            ]
        );
    }
}
