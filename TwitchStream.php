<?php

/**
 * 
 */
class TwitchStream {

    const TWITCH_STREAM_NAMESPACE = 'twitch_stream';

    const STREAM_HAS_STARTED = "%s has started streaming some %s shenanigans. This stream is brought to you by our new sponsor, Charms Blowpops. It's two lollipop treats in one with Charms Blow Pop, a chewy, bubble gum center surrounded by a delicious, fruit-flavored, hard candy shell. You can check it out at https://www.twitch.tv/%s";

    const STREAM_HAS_ENDED = "And that concludes %s's stream. We'd once again like to thank our sponsors, Charms Blowpops.";

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

        $this->logger = new TwitchLogger();
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

        if (! is_null($success_code)) {
            if ($http_code === $success_code) {
                return true;
            } else {
                $message =  "The response code does not match.\n";
                $logger->log_error($message, self::TWITCH_STREAM_NAMESPACE);
                return false;
            }
        }
        if ($result) {
            return json_decode($result, true);
        } else {
            $errno = curl_errno($ch);
            $error_message = curl_strerror($errno);
            $log_info = "cURL error ({$errno}): {$error_message} \n";
            $logger->log_error($log_info, self::TWITCH_STREAM_NAMESPACE);

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

            $exec = $this->invokeTwitchApi($hub_url, $headers, 'POST', $payload, 202);

        } else {
            $message = 'Token needs to be renewed.';
            $logger->log_error($message, self::TWITCH_STREAM_NAMESPACE);
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

        $response = $this->invokeTwitchApi($validation_url, $headers, 'GET');

        if ($response['expires_in'] >= 10000) {
            return true;
        } else {
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

        $url = "https://api.twitch.tv/helix/games?id={$game_id}";
        $headers = ["Client-ID: {$this->twitch_client_id}", "Authorization: Bearer {$this->twitch_auth_token}"];

        $response = $this->invokeTwitchApi($url, $headers, 'GET');

        if ($response) {
            $payload = json_decode($response, true);
            $game_name = $payload[0]['name'];
            return $game_name;
        } else {
            $message = 'Failed API call.';
            $logger->log_error($message, self::TWITCH_STREAM_NAMESPACE);

            return false;
        }

        return $game_name;
    }

    /** 
     * Receives payload from twitch
     * 
     *   // EXAMPLE PAYLOAD FROM ENDPOINT
     *   {
     *   "data": [
     *       {
     *           "id": "0123456789",
     *           "user_id": "5678",
     *           "user_name": "wjdtkdqhs",
     *           "game_id": "21779",
     *           "community_ids": [],
     *           "type": "live",
     *           "title": "Best Stream Ever",
     *           "viewer_count": 417,
     *           "started_at": "2017-12-01T10:09:45Z",
     *           "language": "en",
     *           "thumbnail_url": "https://link/to/thumbnail.jpg"
     *       }]
     *   }
     *
     * @param list $payload : payload from twitch endpoint
     * @return array $discord_payload : array of relevant info
     */
    public function processTwitchStreamPayload(array $payload : array) { 
        $user_id = $payload[0]['user_id'];
        $user_name = $payload[0]['user_name'];
        $game_title = $this->getGameTitle($payload[0]['game_id']);
        $stream_title = $payload[0]['title'];

        $message = '';
        
        if ($payload[0]['type'] === 'live') {
            // the stream has begun

            $message = sprintf(self::STREAM_HAS_STARTED, $user_name, $game_title, $user_name);

        } else {
            // the stream has ended.

            $message = sprintf(self::STREAM_HAS_ENDED, $user_name);
        }

        $this->sendStreamStatus($message);
    }

    /** 
     * Sends a string to the DiscordCommenter class to send to the webhook
     *
     * 
     */
    public function sendStreamStatus(string $message) {
            $discord_commenter = new DiscordCommenter(); 

            $data = ["content" => "{$message}"];
            $payload = json_encode($data);

            $discord_commenter->sendMessage($payload);
    }

}