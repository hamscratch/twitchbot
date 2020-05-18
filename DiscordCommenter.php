<?php

// this is where the payload from twitch will come and report to the discord!
// EXAMPLE PAYLOAD FROM ENDPOINT
/*
{
"data": [{
"id": "0123456789",
"user_id": "5678",
"user_name": "wjdtkdqhs",
"game_id": "21779",
"community_ids": [],
"type": "live",
"title": "Best Stream Ever",
"viewer_count": 417,
"started_at": "2017-12-01T10:09:45Z",
"language": "en",
"thumbnail_url": "https://link/to/thumbnail.jpg"
}]
}
*/


class DiscordCommenter {

    public $id;
    public $user_id;
    public $user_name;
    public $title;
    public $game_id;
    public $type;
    public $started_at;
    public $thumbnail_url;

    public $webhook_url;

    public function __construct(array $data) {
        $this->id = $payload['id'];
        $this->user_id = $payload['user_id'];
        $this->user_name = $payload['user_name'];
        $this->title = $payload['title'];
        $this->game_id = $payload['game_id'];
        $this->type = $payload['type'];
        $this->started_at = $payload['started_at'];
        $this->thumbnail_url = $payload['thumbnail_url'];

        $this->webhook_url = Secrets::DISCORD_WEBHOOK_URL;
    }

    /** 
     * Checks if stream is live and sends a payload to sendMessage()
     *
     * @return array
     */
    public function run() {
        if ($this->type === 'live') {
            $game_title = 'pee'; //$this->getGameTitle($game_id);
            $data = ["content" => "Looks like {$this->user_name} has started streaming their {$game_title} hijinx. You can check out their latest stream at https://www.twitch.tv/{$this->user_name}."];

            $payload = json_encode($data);

            return $this->sendMessage($payload);
        }
    }

    /** 
     * Sends a payload to Discord webhook
     *
     * @param array $payload : contents of Discord webhook payload to send
     * @return bool true : on success of curl
     */
    public  function sendMessage(string $payload) : bool {
        $header = ['content-type: application/json'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        $info = curl_getinfo($ch);
        print_r($info['request_header']);

        $errno = curl_errno($ch);
        $error_message = curl_strerror($errno);
        echo "cURL error ({$errno}): {$error_message} \n";

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $httpCode . "\n";

        if ($result) {
            return json_decode($result, true);
        } else {
            return false;
        }
    }

    /** CURRENTLY NOT IN USE 5/16/20
     * Finds a games title by game_id
     *
     * @param string $game_id : contents of Discord webhook payload to send
     * @return string $result['data']['name'] : name of game
     */
    public function getGameTitle (string $game_id) : string {
        $twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $twitch_client_id = Secrets::TWITCH_CLIENT_ID;

        $url = 'https://api.twitch.tv/helix/games';
        $headers = ["Client-ID: {$twitch_client_id}", "Authorization: Bearer {$twitch_auth_token}"];
        $method = 'GET';

        // fuck i don't have this in this class
        $result = $this->invokeTwitchApi($url, $headers, $game_id, $method);

        return $result['data']['name'];
    }
    
}