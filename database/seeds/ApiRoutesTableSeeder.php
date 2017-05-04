<?php

use App\Models\Route;
use \App\Http\Clients\MbtaRouteClient;
use Illuminate\Database\Seeder;

/**
 * Class ApiRoutesTableSeeder
 */
class ApiRoutesTableSeeder extends Seeder
{
    /** @var MbtaRouteClient */
    private $routeClient;

    /**
     * ApiRoutesTableSeeder constructor.
     * @param MbtaRouteClient $mbtaRouteClient
     */
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

        $mbtaRoutes = json_decode($response->getBody());

        foreach ($mbtaRoutes->mode as $mbtaMode) {
            foreach ($mbtaMode->route as $mbtaRoute) {
                $route = new Route;
                $route->mbta_route_id = $mbtaRoute->route_id;
                $route->mbta_route_name = $mbtaRoute->route_name;
                $route->mbta_route_type = $mbtaMode->route_type;
                $route->mbta_mode_name = $mbtaMode->mode_name;
                $route->save();
            }
        }
    }
}
