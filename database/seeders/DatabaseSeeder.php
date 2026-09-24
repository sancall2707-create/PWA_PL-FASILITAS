<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        User::firstOrCreate(
            ['email' => 'admin@pangudiluhur.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone_number' => '081234567890',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@pangudiluhur.sch.id'],
            [
                'name' => 'Sandi (Guru Informatika)',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone_number' => '089876543210',
            ]
        );

        // 2. Categories
        $categories = ['PG-TK', 'SD', 'SMP', 'SMA', 'Bruderan', 'Gereja', 'Pribadi'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // 3. Vehicles
        $vehicles = [
            ['name' => 'Mobil HIACE', 'plate_number' => 'B 1234 PL', 'series_number' => 'HIACE-2023-01'],
            ['name' => 'Mobil APV', 'plate_number' => 'B 5678 PL', 'series_number' => 'APV-2020-02'],
            ['name' => 'Motor 01', 'plate_number' => 'B 9012 PL', 'series_number' => 'MTR-01'],
            ['name' => 'Motor 02', 'plate_number' => 'B 3456 PL', 'series_number' => 'MTR-02'],
        ];

        foreach ($vehicles as $v) {
            Vehicle::firstOrCreate(['name' => $v['name']], $v);
        }

        // 4. Facilities
        $facilities = [
            ['name' => 'Aula SD', 'location' => 'Gedung SD Lantai 2', 'capacity' => 200],
            ['name' => 'Aula SMP', 'location' => 'Gedung SMP Lantai 3', 'capacity' => 300],
            ['name' => 'Aula SMA', 'location' => 'Gedung SMA Lantai 3', 'capacity' => 400],
            ['name' => 'Lapangan Sekolah', 'location' => 'Area Komplek PL Deltamas', 'capacity' => 1000],
        ];

        foreach ($facilities as $f) {
            Facility::firstOrCreate(['name' => $f['name']], $f);
        }
    }
}
