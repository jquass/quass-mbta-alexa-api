<?php

namespace App\Managers;

use App\Models\Direction;
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
    /**
     * @param string $stopMap
     * @param string|null $destinationMap
     * @return Collection|Stop[]
     */
    public function getStops($stopMap, $destinationMap = null)
    {
        $stops = Stop::with('direction')
            ->whereHas('vocalizations', function ($query) use ($stopMap) {
                $query->where('map', $stopMap);
            })
            ->get();

        return $this->filterStops($stops, $destinationMap);
    }

    /**
     * @param Collection $stops
     * @param string|null $destinationMap
     * @return Collection
     */
    private function filterStops($stops, $destinationMap = null)
    {


        if ($stops->count() > 1 && !empty($destinationMap)) {

            /**
             * Direction matching
             */
            $routeIds = $stops->pluck('route_id')->unique();
            $stops = $this->filterByDirection($stops, $routeIds, $destinationMap);

            if ($stops->count() > 1) {
                /**
                 * Stop matching
                 */
                $stops = $this->filterByStop($stops, $routeIds, $destinationMap);

                // @TODO route matching
            }

        }


        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string|null $destinationMap
     * @return Collection
     */
    private function filterByDirection($stops, $routeIds, $destinationMap = null)
    {
        /** @var Collection $directions */
        $directions = Direction::whereIn('route_id', $routeIds)
            ->whereHas('vocalizations', function ($query) use ($destinationMap) {
                $query->where('map', $destinationMap);
            })
            ->get();

        $directionIds = $directions->pluck('mbta_direction_id')->unique();
        if ($directionIds->count() == 1) {
            $stops = $stops->filter(function ($stop) use ($directionIds) {
                return $stop->direction->mbta_direction_id == $directionIds->first();
            });
        }

        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string|null $destinationMap
     * @return Collection
     */
    private function filterByStop($stops, $routeIds, $destinationMap = null)
    {
        /** @var Collection $destinationStops */
        $destinationStops = Stop::whereIn('route_id', $routeIds)
            ->whereHas('vocalizations', function ($query) use ($destinationMap) {
                $query->where('map', $destinationMap);
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

        return $stops;
    }
}
