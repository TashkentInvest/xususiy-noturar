<?php

namespace Database\Seeders;

use Database\Seeders\DataSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Init\RoleSeeder;
use Database\Seeders\Init\UserSeeder;
use Database\Seeders\Init\RegionsSeeder;
use Database\Seeders\Init\DistrictsSeeder;
use Database\Seeders\init\ExelSeeder;


class SystemInitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                RegionsSeeder::class,
                DistrictsSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                // ExelSeeder::class,
            ]
        );
    }
}
