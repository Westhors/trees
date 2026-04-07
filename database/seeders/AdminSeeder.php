<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        DB::table('admins')->insert(
            [
                [
                    'name' => 'zaid alshaahir',
                    'phone' => '123456789',
                    'email' => 'zaid@alshaahir.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'super_admin' => true,
                    'created_at' => now(),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Fred Tripoli',
                    'phone' => '987654321',
                    'email' => 'fred@wsa-network.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'super_admin' => true,
                    'created_at' => now(),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Remon Sarofeem',
                    'phone' => '1265165161',
                    'email' => 'remon@wsa-network.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'super_admin' => false,
                    'created_at' => now(),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Summer',
                    'phone' => '1265981161',
                    'email' => 'summer@wsa-network.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'super_admin' => false,
                    'created_at' => now(),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Emily',
                    'phone' => '230651890320',
                    'email' => 'emily@wsa-network.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'super_admin' => false,
                    'created_at' => now(),
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
