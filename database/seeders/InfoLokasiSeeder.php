<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfoLokasiSeeder extends Seeder
{
    public function run()
    {
        DB::table('info_lokasi')->insert([
            [
                'nama_tempat' => 'SPKLU Bali 1',
                'deskripsi_tempat' => 'Lokasi SPKLU di Denpasar.',
                'latitude' => -8.650000,
                'longitude' => 115.216667,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_tempat' => 'SPKLU Bali 2',
                'deskripsi_tempat' => 'Lokasi SPKLU di Ubud.',
                'latitude' => -8.519000,
                'longitude' => 115.263000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
