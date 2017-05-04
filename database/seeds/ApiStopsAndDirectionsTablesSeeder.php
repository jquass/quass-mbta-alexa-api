<?php

use App\Http\Clients\MbtaStopsClient;
use App\Models\Direction;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Database\Seeder;

class ApiStopsAndDirectionsTablesSeeder extends Seeder
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

            $mbtaDirections = json_decode($response->getBody());

            foreach ($mbtaDirections->direction as $mbtaDirection) {
                $direction = new Direction;
                $direction->route_id = $route->id;
                $direction->mbta_direction_id = $mbtaDirection->direction_id;
                $direction->mbta_direction_name = $mbtaDirection->direction_name;
                $direction->save();

                foreach ($mbtaDirection->stop as $mbtaStop) {
                    $stop = new Stop;
                    $stop->route_id = $route->id;
                    $stop->direction_id = $direction->id;
                    $stop->mbta_stop_order = $mbtaStop->stop_order;
                    $stop->mbta_stop_id = $mbtaStop->stop_id;
                    $stop->mbta_stop_name = $mbtaStop->stop_name;
                    $stop->mbta_parent_station = $mbtaStop->parent_station;
                    $stop->mbta_parent_station_name = $mbtaStop->parent_station_name;
                    $stop->save();
                }
            }
        }
    }
}
