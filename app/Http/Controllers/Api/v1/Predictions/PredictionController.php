<?php

namespace App\Http\Controllers\Api\v1\Predictions;

use App\Http\Controllers\Api\v1\AbstractMbtaController;
use App\Managers\VocalizationManager;
use Illuminate\Http\Request;

class PredictionController extends AbstractMbtaController
{
    protected static $defaultPath = 'predictionsbystop';

    private $vocalizationManager;

    /**
     * PredictionController constructor.
     * @param VocalizationManager $vocalizationManager
     */
    public function __construct(VocalizationManager $vocalizationManager)
    {
        parent::__construct();
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

        $response = $this->makeGetRequest(
            [
                'stop' => $stop,
            ]
        );

        // @TODO json_decode $response

        $destinationVocalization = $request->input('destination', null);
        $return = [
            '' => '',
        ];
        if (!is_null($destinationVocalization)) {
            $destination = $this->vocalizationManager->getDestination($destinationVocalization);
            $return = [
                '' => $destination,
            ];
        }

        return response()->json(json_decode($return));
    }
}
