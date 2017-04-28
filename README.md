# updater-php
A PHP library for the Updater.com API

by Caleb Tucker-Raymond http://radikosoft.com

# Requirements

Updater PHP requires the [Guzzle PHP library](https://github.com/guzzle/guzzle). 

# Install with composer

    {
        "require": {
            "calebtr/updater-php": "dev-master"
        }
    }

# Example

Simple procedural use:

    $id = 1234;
    $token = '9b53bda4d9417087f8634c31e3b88120';
    $client = new \UpdaterPHP\Client($id, $token);
    
    $client->setBaseUrl('https://api.sandbox.updater.com');
    
    $payload = array( 
        'external_id' => 1234
        'first_name' => 'Don',
        'last_name' => 'Ramon',
        'move_date' => '09/15/2017',
    );
    
    $promise = $client->transaction($payload);
    $response = $promise->wait();
    
    
The **Client::transaction** method can take additional parameters, a callback function for a fulfilled transaction and a callback function for a rejected one. 

Guzzle will pass a Response object to the success callback and an Exception object to the rejected callback.

    $promise = $client->transaction($payload, 'fulfilledCallback', 'rejectedCallback');
    
    function fulfilledCallback($response) {
        echo $response->getStatusCode();
    }
    
    function rejectedCallback($response) {
        echo $response->getMessage();
    }
       
Inject your settings by implementing your own class: 

    class sandboxUpdaterClient extends \UpdaterPHP\Client {
        public function __construct() {
            $id = 1234;
            $token = '9b53bda4d9417087f8634c31e3b88120';
            parent::construct($id, $token);
            $this->setBaseUrl('https://api.sandbox.updater.com');
        }
    }
    
