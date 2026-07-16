<?php

namespace Database\Seeders;

use App\Domain\Region\Models\District;
use App\Domain\Region\Models\Province;
use App\Domain\Region\Models\Regency;
use App\Domain\Region\Models\Village;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $province = Province::query()->firstOrCreate(
            ['code' => '33'],
            [
                'name' => 'Jawa Tengah',
                'is_active' => true,
            ]
        );

        $regency = Regency::query()->firstOrCreate(
            ['code' => '33.29'],
            [
                'province_id' => $province->id,
                'name' => 'Kabupaten Contoh',
                'type' => 'kabupaten',
                'is_active' => true,
            ]
        );

        $districtA = District::query()->firstOrCreate(
            ['code' => '33.29.01'],
            [
                'regency_id' => $regency->id,
                'name' => 'Kecamatan Contoh Utara',
                'is_active' => true,
            ]
        );

        $districtB = District::query()->firstOrCreate(
            ['code' => '33.29.02'],
            [
                'regency_id' => $regency->id,
                'name' => 'Kecamatan Contoh Selatan',
                'is_active' => true,
            ]
        );

        $villages = [
            [
                'district' => $districtA,
                'code' => '33.29.01.2001',
                'name' => 'Desa Mekarsari',
                'type' => 'desa',
            ],
            [
                'district' => $districtA,
                'code' => '33.29.01.1001',
                'name' => 'Kelurahan Sukamaju',
                'type' => 'kelurahan',
            ],
            [
                'district' => $districtB,
                'code' => '33.29.02.2001',
                'name' => 'Desa Harapan',
                'type' => 'desa',
            ],
            [
                'district' => $districtB,
                'code' => '33.29.02.1001',
                'name' => 'Kelurahan Sejahtera',
                'type' => 'kelurahan',
            ],
        ];

        foreach ($villages as $village) {
            Village::query()->firstOrCreate(
                ['code' => $village['code']],
                [
                    'district_id' => $village['district']->id,
                    'name' => $village['name'],
                    'type' => $village['type'],
                    'is_active' => true,
                ]
            );
        }
    }
}
