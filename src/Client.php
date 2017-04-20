<?php

namespace Updater;

use GuzzleHttp\Client as GuzzleClient;

class Client {

    protected $client;

    private $base_url;
    private $integrationID;
    private $version = 'v1';

    private $token;

    public function __construct($id, $token) {
        $this->setIntegrationID($id);
        $this->setToken($token);
        $this->client = new GuzzleClient();
    }

    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function setIntegrationID($id) {
        $this->integrationID = $id;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

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

    public function transaction($payload, callable $callback = null) {

        $url = $this->buildUrl('transactions');

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $this->token
        );

        $options = array(
            'headers' => $headers,
            'body' => json_encode($payload)
        );

        $this->client->requestAsync('POST', $url, $options)->then(
            function($response) {
                if (!empty($callback)) {
                   call_user_func($callback, $response);
                }
            }
        );

    }

}