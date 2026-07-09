<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin/Teknisi User
        $admin = User::create([
            'name' => 'Teknisi Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Customer User
        $customer = User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // Seed some laptop services for the customer
        \App\Models\LaptopService::create([
            'user_id' => $customer->id,
            'device_name' => 'Asus ROG Strix G15',
            'serial_number' => 'SN992847294',
            'complaints' => 'Laptop sering overheat dan mati mendadak saat bermain game berat selama 15 menit.',
            'status' => 'proses',
            'total_cost' => 350000.00,
            'technician_notes' => 'Sedang dilakukan pembersihan heatsink dan penggantian thermal paste menggunakan Thermal Grizzly.',
        ]);

        \App\Models\LaptopService::create([
            'user_id' => $customer->id,
            'device_name' => 'MacBook Pro M2 2023',
            'serial_number' => 'C02F93JMD6R',
            'complaints' => 'Layar LCD berkedip (flicker) setelah beberapa jam pemakaian.',
            'status' => 'pending',
        ]);

        \App\Models\LaptopService::create([
            'user_id' => $customer->id,
            'device_name' => 'Lenovo ThinkPad X1 Carbon',
            'serial_number' => 'L3N9928374',
            'complaints' => 'Keyboard beberapa tombol tidak merespon (huruf A, S, D, F).',
            'status' => 'selesai',
            'total_cost' => 750000.00,
            'technician_notes' => 'Telah dilakukan penggantian satu set keyboard ThinkPad original. Semua tombol sekarang berfungsi normal.',
        ]);
    }
}
