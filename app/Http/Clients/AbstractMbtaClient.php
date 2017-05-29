<?php

namespace App\Http\Clients;

use GuzzleHttp\Client;

abstract class AbstractMbtaClient
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
    public function request($queryParams = [], $path = null)
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
