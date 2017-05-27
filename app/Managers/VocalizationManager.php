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
     * @param string $stopHandle
     * @param string|null $destinationHandle
     * @return Collection|Stop[]
     */
    public function getStops($stopHandle, $destinationHandle = null)
    {
        $stops = Stop::with('direction')
            ->whereHas('vocalizations', function ($query) use ($stopHandle) {
                $query->where('handle', $stopHandle);
            })
            ->get();

        return $this->filterStops($stops, $destinationHandle);
    }

    /**
     * @param Collection $stops
     * @param string|null $destinationHandle
     * @return Collection
     */
    private function filterStops($stops, $destinationHandle = null)
    {
        if ($stops->count() > 1 && !empty($destinationHandle)) {

            /**
             * Direction matching
             */
            $routeIds = $stops->pluck('route_id')->unique();
            $stops = $this->filterByDirection($stops, $routeIds, $destinationHandle);

            if ($stops->count() > 1) {
                /**
                 * Stop matching
                 */
                $stops = $this->filterByStop($stops, $routeIds, $destinationHandle);

                // @TODO route matching
            }

        }


        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string|null $destinationHandle
     * @return Collection
     */
    private function filterByDirection($stops, $routeIds, $destinationHandle = null)
    {
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

        return $stops;
    }

    /**
     * @param Collection $stops
     * @param Collection $routeIds
     * @param string|null $destinationHandle
     * @return Collection
     */
    private function filterByStop($stops, $routeIds, $destinationHandle = null)
    {
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

        return $stops;
    }
}
