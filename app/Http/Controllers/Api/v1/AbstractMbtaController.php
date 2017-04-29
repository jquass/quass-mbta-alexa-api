<?php

namespace App\Http\Controllers\Api\v1;

use GuzzleHttp\Client;
use Laravel\Lumen\Routing\Controller as BaseController;

class AbstractMbtaController extends BaseController
{

    protected static $defaultPath = '';
    protected static $defaultFormat = 'json';

    private static $basePath = 'developer/api/v2';
    private static $url = 'http://realtime.mbta.com';

    private $apiKey;

    /**
     * AbstractMbtaController constructor.
     */
    public function __construct()
    {
        $this->apiKey = env('MBTA_API_KEY');
    }

    /**
     * @param array $queryParams
     * @param null|string $path
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function makeGetRequest($queryParams = [], $path = null)
    {
        $client = new Client();
        return $client->request('GET', $this->getPath($path),
            [
                'query' => array_merge(
                    [
                        'api_key' => $this->apiKey,
                        'format' => static::$defaultFormat,
                    ],
                    $queryParams
                ),
            ]
        );
    }

    /**
     * @param null|string $path
     * @return string
     */
    private function getPath($path = null)
    {
        return implode('/',
            [
                self::$url,
                self::$basePath,
                $path ?: static::$defaultPath,
            ]
        );
    }

}
