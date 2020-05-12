<?php

require __DIR__ . '/' . 'Loader.php';

if (php_sapi_name() ==="cli") {
    if (isset($argv[1])) {
        $twitch_user_id = $argv[1];
    } else {
        $twitch_user_id = Secrets::TWITCH_USER_ID;
    }

    $twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
    $is_valid_token = tokenValidator($twitch_auth_token);

    if ($is_valid_token == TRUE) {

        $twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
        $host_url = Secrets::HOST_URL;
        $hub_url = "https://api.twitch.tv/helix/webhooks/hub";
        $headers = array("Authorization: Bearer {$twitch_auth_token}", "Content-Type: application/json");

        // 864000 seconds = 10 days
        $data = [
            "hub.callback" => "{$host_url}/stream_notificator.php",
            "hub.mode" => "subscribe",
            "hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$twitch_user_id}",
            "hub.lease_seconds" => "864000",
            ];

        $payload = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $hub_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        if ($result) {
            var_dump($result);
            return json_decode($result, true);
        } else {
            return false;
        }
    } else {
        // i should log this failure and report it
        $error_comment = "HALT, NONE SHALL PASS WITHOUT A VALID TOKEN...WHICH YOU DO NOT POSESS.";
        echo $error_comment;
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    return verifyHubChallenge();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_payload = file_get_contents('php://input');
    $payload = json_decode($raw_payload);
    $commenter = new DiscordCommenter($payload);
    
    return $commenter->run();
}

function verifyHubChallenge() {
    $challenge = $_GET['hub.challenge'];
    http_response_code(200);
    echo $challenge;
    exit();
}

function tokenValidator($token) {
    $validation_url = 'https://id.twitch.tv/oauth2/validate';
    $headers = "Authorization: OAuth {$token}";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $validation_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_GET, true);

    $result = curl_exec($ch);

    if ($result) {
        return json_decode($result, true); 
    } else {
        return false;
    }
}

/*
just a thought for future stuff
public function checkNotification() {

}
*/
