<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // * RUN : " php artisan db:seed --class=AdminUserSeeder " to access Dashboard with this Admin DATA
        $adminData = [
            'name' => 'MTM',
            'email' => 'm@gbm.com',
            'password' => Hash::make('m@gbm.com'),
        ];

        DB::table('admins')->insert($adminData);
    }
}
