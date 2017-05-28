<?php

namespace App\Managers;

use App\Models\Direction;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Support\Collection;

/**
 * Class VocalizationManager
 * @package App\Managers
 *
 * Used to take raw vocalizations from Alexa, and identify them
 */
class VocalizationManager
{
    private static $mbtaRouteTypeSubwayGreen = 0;
    private static $mbtaRouteTypeSubway = 1;
    private static $mbtaRouteTypeCommuterRail = 2;
    private static $mbtaRouteTypeBus = 3;
    private static $mbtaRouteTypeBoat = 4;

    /**
     * @var array|null
     */
    private $vocalizationTypes = null;

    public function __construct()
    {
        $this->vocalizationTypes = [
            'train' => [self::$mbtaRouteTypeSubwayGreen, self::$mbtaRouteTypeSubway, self::$mbtaRouteTypeCommuterRail],
            'subway' => [self::$mbtaRouteTypeSubwayGreen, self::$mbtaRouteTypeSubway],
            'commuter rail' => [self::$mbtaRouteTypeCommuterRail],
            'bus' => [self::$mbtaRouteTypeBus],
            'boat' => [self::$mbtaRouteTypeBoat],
        ];
    }

    /**
     * @param string $stopHandle
     * @param string|null $destinationHandle
     * @param string|null $type
     * @return Collection|Stop[]
     */
    public function getStops($stopHandle, $destinationHandle = null, $type = null)
    {
        if (!empty($type) && array_key_exists($type, $this->vocalizationTypes)) {
            $types = $this->vocalizationTypes[$type];
            $stops = Stop::with('direction')
                ->whereHas('route', function ($query) use ($types) {
                    $query->whereIn('mbta_route_type', $types);
                })
                ->whereHas('vocalizations', function ($query) use ($stopHandle) {
                    $query->where('handle', $stopHandle);
                })
                ->get();
        } else {
            $stops = Stop::with('direction')
                ->whereHas('vocalizations', function ($query) use ($stopHandle) {
                    $query->where('handle', $stopHandle);
                })
                ->get();
        }

        return $this->filterStops($stops, $destinationHandle);
    }

    /**
     * @param Collection $stops
     * @param string|null $destinationHandle
     * @return Collection
     */
    private function filterStops($stops, $destinationHandle = null)
    {
        if (!empty($destinationHandle)) {
            $routeIds = $stops->pluck('route_id')->unique();

            $stops = $this->filterByDirection($stops, $routeIds, $destinationHandle);
            $stops = $this->filterByStop($stops, $routeIds, $destinationHandle);
            $stops = $this->filterByRoute($stops, $routeIds, $destinationHandle);
        }

        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string $destinationHandle
     * @return Collection
     */
    private function filterByDirection($stops, $routeIds, $destinationHandle)
    {
        if ($stops->pluck('mbta_stop_id')->unique()->count() > 1) {
            /** @var Collection $directions */
            $directions = Direction::whereIn('route_id', $routeIds)
                ->whereHas('vocalizations', function ($query) use ($destinationHandle) {
                    $query->where('handle', $destinationHandle);
                })
                ->get();

            $directionIds = $directions->pluck('mbta_direction_id')->unique();
            if ($directionIds->count() == 1) {
                $stops = $stops->filter(function ($stop) use ($directionIds) {
                    return $stop->direction->mbta_direction_id == $directionIds->first();
                });
            }
        }

        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string $destinationHandle
     * @return Collection
     */
    private function filterByStop($stops, $routeIds, $destinationHandle)
    {
        if ($stops->pluck('mbta_stop_id')->unique()->count() > 1) {
            /** @var Collection $destinationStops */
            $destinationStops = Stop::whereIn('route_id', $routeIds)
                ->whereHas('vocalizations', function ($query) use ($destinationHandle) {
                    $query->where('handle', $destinationHandle);
                })
                ->get();

            if ($destinationStops->isNotEmpty()) {
                /** @var Stop $stop */
                $stops = $stops->filter(function ($stop) use ($destinationStops) {
                    return $destinationStops->where('route_id', '==', $stop->route_id)
                        ->where('direction_id', '=', $stop->direction_id)
                        ->where('mbta_stop_order', '>', $stop->mbta_stop_order)
                        ->isNotEmpty();
                });
            }
        }

        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string $destinationHandle
     * @return Collection
     */
    private function filterByRoute($stops, $routeIds, $destinationHandle)
    {
        if ($stops->pluck('mbta_stop_id')->unique()->count() > 1) {
            /** @var Collection $routes */
            $routes = Route::whereIn('id', $routeIds)
                ->whereHas('vocalizations', function ($query) use ($destinationHandle) {
                    $query->where('handle', $destinationHandle);
                })
                ->get();

            if ($routes->isNotEmpty()) {
                /** @var Stop $stop */
                $stops = $stops->filter(function ($stop) use ($routes) {
                    return $routes->where('id', '==', $stop->route_id)
                        ->isNotEmpty();
                });
            }
        }

        return $stops;
    }
}
