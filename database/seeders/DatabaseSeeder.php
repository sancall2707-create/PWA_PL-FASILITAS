<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Facility;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users - SECURE: Use env vars for passwords, generate random if not set
        // Admin
        $adminPass = env('ADMIN_DEFAULT_PASSWORD') ?? Str::random(16);
        User::firstOrCreate(
            ['email' => 'admin@pangudiluhur.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => Hash::make($adminPass),
                'role' => 'admin',
                'phone_number' => '081234567890',
            ]
        );

        // User
        $userPass = env('USER_DEFAULT_PASSWORD') ?? Str::random(16);
        User::firstOrCreate(
            ['email' => 'user@pangudiluhur.sch.id'],
            [
                'name' => 'Sandi (Guru Informatika)',
                'password' => Hash::make($userPass),
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

        // 5. Vehicle Unit Names (Update from seeder)
        $vehicleUpdates = [
            ['name' => 'Motor 01', 'new_name' => 'Motor Tosa', 'new_plate' => 'TOSA-01'],
            ['name' => 'Motor 02', 'new_name' => 'Motor Supra', 'new_plate' => 'SUPRA-02'],
        ];
        foreach ($vehicleUpdates as $vu) {
            Vehicle::where('name', $vu['name'])->update([
                'name' => $vu['new_name'],
                'plate_number' => $vu['new_plate'],
            ]);
        }
    }
}