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

    public function transaction($payload, $fulfilled = null,  $rejected = null) {
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