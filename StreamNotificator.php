<?php

/**
 * 
 */
class StreamNotificator {

    public $twitch_client_id;
    public $twitch_auth_token;
    public $host_url;
    public $endpoint_url;

    public function __construct() {
        $this->twitch_client_id = Secrets::TWITCH_CLIENT_ID;
        $this->twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $this->host_url = Secrets::HOST_URL;
        $this->endpoint_url = Secrets::ENDPOINT_URL;
    }

    /** 
     * Makes an API call to twitch
     * 
     * @param string $url : url for request
     * @param array $headers : headers for request
     * @param array $parameters : additional stuff
     * @param string $method : GET, POST
     * @return bool true : no idea
     */
    public function invokeTwitchApi (string $url, array $headers, string $method, string $parameters = NULL) {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $errno = curl_errno($ch);
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}): {$error_message} \n";

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpCode . "\n";

        if ($result) {
            var_dump($result);
            return json_decode($result, true);
        } else {
            echo "request to {$url} failed \n";
            var_dump($result);
            return false;
        }
    }

    /** 
     * Subscribes to a given user's id stream.
     * Checks to see if auth token is valid first.
     * 
     * @param string $twitch_user_id : a user's twitch id
     * @return mixed 
     */
    public function subscribeToUser(string $twitch_user_id) {
        
        $is_valid_token = $this->isTokenValid($this->twitch_auth_token);

        if ($is_valid_token) {
            $hub_url = "https://api.twitch.tv/helix/webhooks/hub";
            $headers = ["Authorization: Bearer {$this->twitch_auth_token}", "Client-ID: {$this->twitch_client_id}", "Content-Type: application/json"];

            // 864000 seconds = 10 days
            $data = [
                "hub.callback" => "{$this->host_url}/{$this->endpoint_url}}",
                "hub.mode" => "subscribe",
                "hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$twitch_user_id}",
                "hub.lease_seconds" => "864000",
                ];

            $payload = json_encode($data);

            echo "sending subscribing request \n";
            $exec = $this->invokeTwitchApi($hub_url, $headers, 'POST', $payload);
            var_dump($exec);

        } else {
            // i should log this failure and report it
            $error_comment = "HALT, NONE SHALL PASS WITHOUT A VALID TOKEN...WHICH YOU DO NOT POSESS.";
            echo $error_comment;
            exit();
        }
    }

    /** 
     * Checks if a token is valid
     * 
     * @param string $token : current token
     * @return bool true : if token is valid
     */
    public function isTokenValid(string $token) : bool {
        /*
        Response body:
            {
            "client_id":"<CLIENT_ID>",
            "scopes":[],
            "expires_in":4536347
             }
        */

        $validation_url = 'https://id.twitch.tv/oauth2/validate';
        $headers = ["Authorization: OAuth {$token}"];
        $method = 'GET';

        $response = $this->invokeTwitchApi($validation_url, $headers, $method);

        if ($response['expires_in'] >= 10000) {
            return true;
        } else {
            // return $this->renewToken();
            return false;
        }
    }

    /*
    just a thought for future stuff
    public function checkNotification() {

    }
    */
}