<?php

namespace Fused\Zoom;

use GuzzleHttp\Client;

class Zoom
{
    protected $apiKey;
    protected $apiSecret;
    protected $apiUrl = "https://api.zoom.us/v1/";
    protected $client;
    protected $resource = [];
    protected $errorCodes = [
        102 => "ZoomAuthorizationException",
    ];

    public function __construct($config)
    {
        $this->apiKey = $config['key'];
        $this->apiSecret = $config['secret'];

        if (isset($config['apiUrl'])) {
            $this->apiUrl = $config['apiUrl'];
        }

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
        ]);
    }

    public function sendRequest($action, $data = [])
    {
        $data = array_merge([
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'data_type' => 'JSON',
        ], $data);

        $response = $this->client->request('POST', implode($this->resource, '/').'/'.$action, [
            'form_params' => $data,
        ]);
        $object = json_decode($response->getBody());

        // Clear resource array
        $this->resource = [];

        if (isset($object->error)) {
            $this->handleError($object->error);
        }

        return $object;
    }

    protected function handleError($error)
    {
        if (array_key_exists($error->code, $this->errorCodes)) {
            $class = "Fused\Zoom\Exceptions\\".$this->errorCodes[$error->code];

            if (class_exists($class)) {
                throw new $class($error->message);
            }
        }

        throw new Exceptions\ZoomException($error->message);
    }

    public function __get($name)
    {
        $this->resource[] = $name;

        return $this;
    }

    public function __call($name, $args)
    {
        $args = array_merge([
            $name,
        ], $args);

        return call_user_func_array([$this, 'sendRequest'], $args);
    }
}
