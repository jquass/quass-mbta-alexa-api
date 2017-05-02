<?php

use App\Models\Route;
use \App\Http\Clients\MbtaRouteClient;
use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{
    /** @var MbtaRouteClient */
    private $routeClient;

    public function __construct(MbtaRouteClient $mbtaRouteClient)
    {
        $this->routeClient = $mbtaRouteClient;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = $this->routeClient->request();
        if ($response->getStatusCode() != 200) {
            die('Failed to get response from MBTA API');
        }

        $routes = json_decode($response->getBody());

        foreach ($routes->mode as $mode) {
            foreach ($mode->route as $route) {
                $newRoute = new Route;
                $newRoute->mbta_route_id = $route->route_id;
                $newRoute->mbta_route_name = $route->route_name;
                $newRoute->mbta_route_type = $mode->route_type;
                $newRoute->mbta_mode_name = $mode->mode_name;
                $newRoute->save();
            }
        }
    }
}
