<?php

if (php_sapi_name() == "cli") {
    $twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;
    $twitch_user_id = Secrets::TWITCH_USER_UD;
    $host_url = Secrets::HOST_URL;

    $url = "https://api.twitch.tv/helix/webhooks/hub";
    $headers = array("Authorization: Bearer {$twitch_auth_token}", "Content-Type: application/json");

    $data = [
        "hub.callback" => "{$host_url}/stream_notificator.php",
        "hub.mode" => "subscribe",
        "hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$twitch_user_id}",
        "hub.lease_seconds" => "864000",
        ];

    $payload = json_encode($data);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
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
    $challenge = $_GET['hub.challenge'];
    http_response_code(200);
    echo $challenge;
    exit();
}   