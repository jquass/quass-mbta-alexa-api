<?php

use App\Http\Clients\MbtaStopsClient;
use App\Models\Direction;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Database\Seeder;

class StopsAndDirectionsTablesSeeder extends Seeder
{
    /** @var MbtaStopsClient */
    private $stopClient;

    public function __construct(MbtaStopsClient $mbtaStopsClient)
    {
        $this->stopClient = $mbtaStopsClient;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::all();

        foreach ($routes as $route) {
            $response = $this->stopClient->request(['route' => $route->mbta_route_id]);
            if ($response->getStatusCode() != 200) {
                echo("Unable to seed for route: {$route->mbta_route_id}");
                continue;
            }

            $directions = json_decode($response->getBody());

            foreach ($directions->direction as $direction) {
                $newDirection = new Direction;
                $newDirection->route_id = $route->id;
                $newDirection->mbta_direction_id = $direction->direction_id;
                $newDirection->mbta_direction_name = $direction->direction_name;
                $newDirection->save();

                foreach ($direction->stop as $stop) {
                    $newStop = new Stop;
                    $newStop->route_id = $route->id;
                    $newStop->direction_id = $newDirection->id;
                    $newStop->mbta_stop_order = $stop->stop_order;
                    $newStop->mbta_stop_id = $stop->stop_id;
                    $newStop->mbta_stop_name = $stop->stop_name;
                    $newStop->mbta_parent_station = $stop->parent_station;
                    $newStop->mbta_parent_station_name = $stop->parent_station_name;
                    $newStop->save();
                }
            }
        }
    }
}
