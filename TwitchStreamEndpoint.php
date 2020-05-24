<?php

// Endpoint for receiving a Twitch stream payload

require __DIR__ . '/' . 'Loader.php';

$twitch_stream = new TwitchStream();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // if we get a challenge for our subscription via GET request

    $twitch_stream->verifyHubChallenge();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if we get a payload with stream info via POST request

    $raw_payload = file_get_contents('php://input');
    $twitch_payload = json_decode($raw_payload, true);

    if (! is_null($twitch_payload)) {
        $twitch_stream->processTwitchStreamPayload($twitch_payload);
    } 
}
