<?php

namespace Database\Seeders;

use App\Models\Kamar;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        $kamars = [
            [
                'tipe_kamar' => 'Standard',
                'nomor_kamar' => '101',
                'fasilitas' => 'Tempat tidur, lemari, WiFi',
                'harga' => 750000,
            ],
            [
                'tipe_kamar' => 'Standard',
                'nomor_kamar' => '102',
                'fasilitas' => 'Tempat tidur, lemari, WiFi',
                'harga' => 750000,
            ],
            [
                'tipe_kamar' => 'Deluxe',
                'nomor_kamar' => '201',
                'fasilitas' => 'AC, tempat tidur, lemari, WiFi',
                'harga' => 1200000,
            ],
            [
                'tipe_kamar' => 'Deluxe',
                'nomor_kamar' => '202',
                'fasilitas' => 'AC, tempat tidur, lemari, WiFi',
                'harga' => 1200000,
            ],
            [
                'tipe_kamar' => 'VIP',
                'nomor_kamar' => '301',
                'fasilitas' => 'AC, kamar mandi dalam, tempat tidur, lemari, WiFi',
                'harga' => 1750000,
            ],
        ];

        foreach ($kamars as $kamar) {
            Kamar::query()->firstOrCreate(
                ['nomor_kamar' => $kamar['nomor_kamar']],
                [...$kamar, 'status' => 'tersedia']
            );
        }
    }
}
