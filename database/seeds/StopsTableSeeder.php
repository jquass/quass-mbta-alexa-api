<?php

use \Flynsarmy\CsvSeeder\CsvSeeder;

/**
 * Class StopsTableSeeder
 */
class StopsTableSeeder extends CsvSeeder
{
    /**
     * StopsTableSeeder constructor.
     */
    public function __construct()
    {
        $this->table = 'stops';
        $this->filename = base_path() . '/database/seeds/csvs/stops.csv';
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table($this->table)->truncate();

        parent::run();
    }
}
