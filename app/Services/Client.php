<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Client
{
    protected $client;

    public $data;

    public function __construct()
    {
        $config = [
            'base_uri' => env('PAYMENT_URL', 'https://test-api.zotlo.com/v1/'),
            'headers' => [
                'AccessKey'         => env('PAYMENT_KEY', '3947a9bdb6c565258844deba5e0e25cb5975bf82eded1ea1bd'),
                'AccessSecret'      => env('PAYMENT_SECRET', 'af04af0196348d9381c1afa67dabc2121ede34b29496110e8307b97382dd93b1d2242263a8b7b47d'),
                'Application_id'    => 128,
                'Language'          => 'tr'
            ]
        ];

        $this->client = new GuzzleClient($config);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscriptionRequest()
    {
        try {
            $request = $this->client->post('payment/credit-card', [
                'json' => $this->data
            ]);

            return [
                'status' => true,
                'data' => json_decode($request->getBody(), 1)
            ];

        } catch (ClientException $exception) {

            $response = $exception->getResponse();

            return [
                'status' => false,
                'data' => [],
                'message' => json_decode($response->getBody(), 1)
            ];
        }
    }

    public function getSubscriptionProfile()
    {
        try {
            $request = $this->client->get('subscription/profile', [
                'query' => http_build_query($this->data)
            ]);

            return [
                'status' => true,
                'data' => json_decode($request->getBody(), 1)
            ];

        } catch (ClientException $exception) {

            $response = $exception->getResponse();

            return [
                'status' => false,
                'data' => [],
                'message' => json_decode($response->getBody(), 1)
            ];
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscriptionRequest()
    {
        try {
            $request = $this->client->post('subscription/cancellation', [
                'json' => $this->data
            ]);

            return [
                'status' => true,
                'data' => json_decode($request->getBody(), 1)
            ];

        } catch (ClientException $exception) {

            $response = $exception->getResponse();

            return [
                'status' => false,
                'data' => [],
                'message' => json_decode($response->getBody(), 1)
            ];
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function savedCardListRequest()
    {
        try {
            $request = $this->client->get('subscription/card-list', [
                'query' => http_build_query($this->data)
            ]);

            return [
                'status' => true,
                'data' => json_decode($request->getBody(), 1)
            ];

        } catch (ClientException $exception) {

            $response = $exception->getResponse();

            return [
                'status' => false,
                'data' => [],
                'message' => json_decode($response->getBody(), 1)
            ];
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }
}
