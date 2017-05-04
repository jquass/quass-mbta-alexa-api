<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoutesTableSeeder::class);
        $this->call(DirectionsTableSeeder::class);
        $this->call(StopsTableSeeder::class);
    }
}
