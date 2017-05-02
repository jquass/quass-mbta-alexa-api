<?php

namespace App\Managers;

/**
 * Class VocalizationManager
 * @package App\Managers
 *
 * Used to take raw vocalizations from Alexa, and identify them
 */
class VocalizationManager
{

    /**
     * @param string $vocalization
     * @return string
     */
    public function getDestination($vocalization)
    {
        return $vocalization;
    }

    /**
     * @param string $vocalization
     * @return string
     */
    public function getStop($vocalization)
    {
        return $vocalization;
    }

}
