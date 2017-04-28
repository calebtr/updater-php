<?php

namespace Updater;

use GuzzleHttp\Client as GuzzleClient;

class Client {

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string
     */
    private $base_url;

    /**
     * @var string
     */
    private $integrationID;

    /**
     * @var string
     */
    private $version = 'v1';

    /**
     * @var string
     */
    private $token;

    /**
     * Client constructor.
     * @param string $id
     * @param string $token
     */
    public function __construct($id, $token) {
        $this->setIntegrationID($id);
        $this->setToken($token);
        $this->client = new GuzzleClient();
    }

    /**
     * @param string $base_url
     */
    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * @param string $id
     */
    public function setIntegrationID($id) {
        $this->integrationID = $id;
    }

    /**
     * @param string $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * Builds a url string for the Updater.com API.
     *
     * @param $method
     * @return string
     */
    private function buildUrl($method) {
        $elements = array(
            $this->base_url,
            $this->version,
            'integrations',
            $this->integrationID,
            $method
        );
        return implode('/', $elements);
    }

    /**
     * Submits a single transaction to the Updater.com API.
     *
     * $payload should include an 'external_id' key.
     *
     * @param array $payload
     * @param null | callable $fulfilled
     * @param null | callable $rejected
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function transaction(array $payload, $fulfilled = null,  $rejected = null) {
        $url = $this->buildUrl('transactions');

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $this->token
        );

        $options = array(
            'headers' => $headers,
            'body' => json_encode(array('payload' => $payload, 'external_id' => $payload['external_id']))
        );

        $promise = $this->client->requestAsync('POST', $url, $options)->then(
            function($r) use ($fulfilled) {
                if (!empty($fulfilled)) {
                   call_user_func($fulfilled, $r);
                }
            },
            function ($r) use ($rejected) {
                if (!empty($rejected)) {
                    call_user_func($rejected, $r);
                }
            }
        );

        return $promise;

    }

}