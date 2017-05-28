<?php

namespace App\Http\Controllers\Api\v1;

use App\Managers\PredictionManager;
use App\Managers\VocalizationManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class PredictionController extends BaseController
{
    private $predictionManager;
    private $vocalizationManager;

    /**
     * PredictionController constructor.
     * @param PredictionManager $predictionManager
     * @param VocalizationManager $vocalizationManager
     */
    public function __construct(
        PredictionManager $predictionManager,
        VocalizationManager $vocalizationManager
    ) {
        $this->predictionManager = $predictionManager;
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
                'stop' => 'required|string|exists:vocalizations,handle',
                'destination' => 'sometimes|string|exists:vocalizations,handle',
                'type' => 'sometimes|string'
            ]
        );

        $stops = $this->vocalizationManager->getStops(
            $request->input('stop'),
            $request->input('filter', null),
            $request->input('type', null)
        );

        if ($stops->isEmpty()) {
            $return = ['error' => 'Vocalization did not match any stop.'];
            $code = 422;
        } else {
            $return = $this->predictionManager->createPredictions($stops, $request);
            $code = 201;
        }

        return response()->json($return, $code);
    }
}
