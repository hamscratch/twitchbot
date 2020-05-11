<? php

require "Secrets.php";

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

$webhook_url = Secrets::DISCORD_WEBHOOK_URL;

$payload = file_get_contents('php://input');

public $id;
public $user_id;
public $user_name;
public $title;
public $game_id;
public $type;
public $started_at;
public $thumbnail_url;

class DiscordCommenter {

    public function __construct($payload) {
        $this->id = $payload['id'];
        $this->user_id = $payload['user_id'];
        $this->user_name = $payload['user_name'];
        $this->title = $payload['title'];
        $this->game_id = $payload['game_id'];
        $this->type = $payload['type'];
        $this->started_at = $payload['started_at'];
        $this->thumbnail_url = $payload['thumbnail_url'];
    }

    if ($type == 'live') {
        $payload = "Looks like {$user_name} has started streaming. You can check out their latest stream at https://www.twitch.tv/{$user_name}."
        return $this->sendMessage($payload);
    }

    public sendMessage($payload) {
        $header = ['content-type': 'application/json'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        if ($result) {
            return json_decode($result, true);
        } else {
            return false;
        }
    }

    /* pipe dreams
    public function getGameTitle () {

    }
    */
}