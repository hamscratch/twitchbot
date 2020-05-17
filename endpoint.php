<?php

/**
 *
 *
 *
 *
 * 
 */

require __DIR__ . '/' . 'Loader.php';

$stream_notificator = new StreamNotificator();

if (count($argv) > 1) {
    $twitch_user_id = $argv[1];
    $stream_notificator->subscribeToUser($twitch_user_id);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // if we get a challenge for our subscription via GET request

    verifyHubChallenge();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if we get a payload with stream info via POST request

    $raw_payload = file_get_contents('php://input');
    $payload = json_decode($raw_payload);

    $discord_commenter = new DiscordCommenter($payload);
    $discord_commenter->run();
}

/** 
 * If file receives a GET request, this will verify the 
 * hub challenge. 
 *
 * @return : void
 */
function verifyHubChallenge() : void {
    $challenge = $_GET['hub.challenge'];
    http_response_code(200);
    print_r($challenge);
    exit();
}
