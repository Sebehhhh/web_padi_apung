<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nip_nik'   => '198706112023001',
                'name'      => 'Super Admin',
                'position'  => 'Administrator',
                'division'  => 'IT',
                'address'   => 'Jl. Pahlawan No.1',
                'role'      => 'admin',
                'photo_url' => null,
                'email'     => 'admin@example.com',
                'password'  => Hash::make('password'), // Password: passwordadmin123
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'nip_nik'   => '199003222023002',
                'name'      => 'Kepala Sawah',
                'position'  => 'Kepala',
                'division'  => 'Operasional',
                'address'   => 'Jl. Tani No.5',
                'role'      => 'kepala',
                'photo_url' => null,
                'email'     => 'kepala@example.com',
                'password'  => Hash::make('password'), // Password: passwordkepala123
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'nip_nik'   => '200110202023003',
                'name'      => 'Pegawai Lapangan',
                'position'  => 'Pegawai',
                'division'  => 'Lapangan',
                'address'   => 'Desa Maju Jaya',
                'role'      => 'pegawai',
                'photo_url' => null,
                'email'     => 'pegawai@example.com',
                'password'  => Hash::make('password'), // Password: passwordpegawai123
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);

    }
}
