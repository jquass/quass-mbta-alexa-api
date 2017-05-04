<?php

namespace App\Managers;

use App\Http\Clients\MbtaPredictionClient;
use Illuminate\Support\Collection;

class PredictionManager
{
    private $predictionClient;

    /**
     * MbtaManager constructor.
     * @param MbtaPredictionClient $predictionClient
     */
    public function __construct(MbtaPredictionClient $predictionClient)
    {
        $this->predictionClient = $predictionClient;
    }

    /**
     * @param Collection $stops
     * @return \stdClass[]
     */
    public function createPredictions($stops)
    {
        $predictions = [];
        foreach ($stops as $stop) {
            $response = $this->predictionClient->request(['stop' => $stop->mbta_stop_id]);
            if ($response->getStatusCode() == 200) {
                $prediction = json_decode($response->getBody());
                if (!empty($prediction)) {
                    $predictions[] = $prediction;
                }
            }
        }

        // @TODO Store predictions

        return $predictions;
    }

}
