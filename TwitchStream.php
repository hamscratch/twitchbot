<?php

/**
 * 
 */
class TwitchStream {
    const EMPTY_RESPONSE_STRING = '';

    public $twitch_client_id;
    public $twitch_auth_token;
    public $host_url;
    public $endpoint_url;

    public $logger;

    public function __construct() {
        $this->twitch_client_id = Secrets::TWITCH_CLIENT_ID;
        $this->twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $this->host_url = Secrets::HOST_URL;
        $this->endpoint_path = Secrets::ENDPOINT_PATH;

        $this->logger = new Logger();
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
    private function invokeTwitchApi (string $url, array $headers, string $method, string $parameters = NULL, $success_code = NULL) {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        print_r($info['request_header']);

        $errno = curl_errno($ch);
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}): {$error_message} \n";

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $http_code . "\n";

        if (! is_null($success_code)) {
            if ($http_code === $success_code) {
                return true;
            } else {
                echo "The response code does not match.\n";
                return false;
            }
        }

        if ($result) {
            return json_decode($result, true);
        } else {
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
                "hub.callback" => "{$this->host_url}" . "{$this->endpoint_path}",
                "hub.mode" => "subscribe",
                "hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$twitch_user_id}",
                "hub.lease_seconds" => "864000",
                ];

            $payload = json_encode($data);

            echo "sending subscribing request \n";
            $exec = $this->invokeTwitchApi($hub_url, $headers, 'POST', $payload, 202);
            var_dump($exec);

        } else {
            $log_data = self::buildFailureLog('subscribeToUser', 'Token is not valid.');
            $this->logger->writeToLogs($log_data);
        }
    }

    /** 
     * If file receives a GET request, this will verify the 
     * hub challenge. 
     *
     * @return : void
     */
    public function verifyHubChallenge($challenge) : void {
        header("Content-Type: text/plain");
        http_response_code(200);
        echo $challenge;
    }

    /** 
     * Checks if a token is valid
     * 
     * @param string $token : current token
     * @return bool true : if token is valid
     */
    private function isTokenValid(string $token) : bool {
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

    /** 
     * Finds a games title by game_id
     *
     * @param string $game_id : contents of Discord webhook payload to send
     * @return string $game_name : name of game
     */
    public function getGameTitle (string $game_id) : string {

        $twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $twitch_client_id = Secrets::TWITCH_CLIENT_ID;

        $url = "https://api.twitch.tv/helix/games?id={$game_id}";
        $headers = ["Client-ID: {$twitch_client_id}", "Authorization: Bearer {$twitch_auth_token}"];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_GET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);

        if ($result) {
            $payload = json_decode($result, true);
            $game_name = $payload[0]['name'];
            return $game_name;
        } else {
            return false;
        }

        return $game_name;
    }

    /** 
     * Receives payload from twitch and packages it nice
     * 
     * @param list $payload : current token
     * @return array $discord_payload : array of relevant info
     */
    public function processTwitchPayload(array $payload : array) {

    }

}