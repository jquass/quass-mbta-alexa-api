<?php

use Illuminate\Database\Seeder;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds that get fresh data from the MBTA API.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ApiRoutesTableSeeder::class);
        $this->call(ApiStopsAndDirectionsTablesSeeder::class);
    }
}
