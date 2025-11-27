<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageLocation;

class StorageLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kita akan buat lokasi A-D, Rak 1-5, Baris 1-10
        // Menggunakan firstOrCreate agar aman dijalankan berkali-kali

        $racks = ['A', 'B', 'C', 'D'];
        $shelves = 5;
        $rows = 10;

        foreach ($racks as $rackLetter) {
            for ($shelf = 1; $shelf <= $shelves; $shelf++) {
                for ($row = 1; $row <= $rows; $row++) {
                    
                    // Format kode: A-01-R1
                    $locationCode = sprintf('%s-%02d-R%d', $rackLetter, $shelf, $row);

                    // PERBAIKAN: Gunakan firstOrCreate
                    // Ini akan mencari 'location_code', 
                    // dan hanya membuatnya jika belum ada.
                    StorageLocation::firstOrCreate(
                        ['location_code' => $locationCode], // Kunci unik untuk dicari
                        [
                            'capacity' => 100, // Data yang diisi jika belum ada
                            'is_filled' => false,
                        ]
                    );
                }
            }
        }
    }
}