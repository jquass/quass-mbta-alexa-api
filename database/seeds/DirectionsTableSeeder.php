<?php

use \Flynsarmy\CsvSeeder\CsvSeeder;

/**
 * Class DirectionsTableSeeder
 */
class DirectionsTableSeeder extends CsvSeeder
{
    /**
     * DirectionsTableSeeder constructor.
     */
    public function __construct()
    {
        $this->table = 'directions';
        $this->filename = base_path() . '/database/seeds/csvs/directions.csv';
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
