<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Clients\MbtaPredictionClient;
use App\Managers\DirectionManager;
use App\Managers\VocalizationManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class PredictionController extends BaseController
{
    private $directionManager;
    private $predictionClient;
    private $vocalizationManager;

    /**
     * PredictionController constructor.
     * @param DirectionManager $directionManager
     * @param MbtaPredictionClient $predictionClient
     * @param VocalizationManager $vocalizationManager
     */
    public function __construct(
        DirectionManager $directionManager,
        MbtaPredictionClient $predictionClient,
        VocalizationManager $vocalizationManager
    ) {
        $this->directionManager = $directionManager;
        $this->predictionClient = $predictionClient;
        $this->vocalizationManager = $vocalizationManager;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'stop' => 'required',
                'destination' => '',
            ]
        );

        $stop = $this->vocalizationManager->getStop($request->input('stop'));
        $response = $this->predictionClient->request(
            [
                'stop' => $stop,
            ]
        );

        $return = json_decode($response->getBody());

        switch ($response->getStatusCode()) {

            case 200: // All good
                $destinationVocalization = $request->input('destination', null);
                if (!is_null($destinationVocalization)) {
                    $destination = $this->vocalizationManager->getDestination($destinationVocalization);
                    $return->direction = $this->directionManager->getDirection($stop, $destination);
                }
                // @TODO Create Prediction
                $code = 201;
                break;

            case 404: // Stop not found
                $code = 404;
                break;

            case 401: // API Key is not set
            default:  // Something went wrong
                $code = 502;
                break;
        }

        return response()->json($return, $code);
    }
}
