<?php

require __DIR__ . '/' . 'Loader.php';

/**
 * 
 */
class StreamNotificator {

    public $twitch_auth_token;
    public $host_url;

    public function __construct() {
        $this->twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $this->host_url = Secrets::HOST_URL;
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
    public function invokeTwitchApi (string $url, array $headers, string $method, array $parameters = false) : array {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if ($result) {
            return json_decode($result, true);
        } else {
            return false;
        }
    }

    /** 
     * depending on the scenario, it will do one of the following:
     *
     * 1) If $twitch_user_id is set to TRUE, it will attempt to subscribe
     *    to given user's activity stream.
     * 2) If a GET request is sent to the file, it will run verifyHubChallenge()
     *    to verify subscription.
     * 3) If a POST request is sent to the file, it will send the received
     *    payload to discordCommenter to send a message to Discord server.
     * 
     * @param string $twitch_user_id : a user's twitch id
     * @return mixed 
     */
    public function run (string $twitch_user_id = false) {
        // if this is being run from the host with a user_id
        
        if ($twitch_user_id) {

            $is_valid_token = $this->isTokenValid($twitch_auth_token);

            if ($is_valid_token) {
                $hub_url = "https://api.twitch.tv/helix/webhooks/hub";
                $headers = ["Authorization: Bearer {$twitch_auth_token}", "Content-Type: application/json"];

                // 864000 seconds = 10 days
                $data = [
                    "hub.callback" => "{$host_url}/stream_notificator.php",
                    "hub.mode" => "subscribe",
                    "hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$twitch_user_id}",
                    "hub.lease_seconds" => "864000",
                    ];

                $payload = json_encode($data);

                $exec = $this->invokeTwitchApi($hub_url, $headers, 'POST', $payload);
            } else {
                // i should log this failure and report it
                $error_comment = "HALT, NONE SHALL PASS WITHOUT A VALID TOKEN...WHICH YOU DO NOT POSESS.";
                echo $error_comment;
                exit();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // if we get a challenge for our subscription via GET request
            
            $this->verifyHubChallenge();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // if we get a payload with stream info via POST request

            $raw_payload = file_get_contents('php://input');
            $payload = json_decode($raw_payload);
            $commenter = new DiscordCommenter($payload);
            
            $commenter->run();
        }
    }

    /** 
     * If file receives a GET request, this will verify the 
     * hub challenge. 
     *
     * @return : void
     */
    public function verifyHubChallenge() : void {
        $challenge = $_GET['hub.challenge'];
        http_response_code(200);
        echo $challenge;
        exit();
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
        $headers = "Authorization: OAuth {$token}";
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