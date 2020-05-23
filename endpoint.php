<?php

/**
 * Receives a payload from Twitch and sends it to DiscordCommenter
 *
 */

require __DIR__ . '/' . 'Loader.php';

$twitch_stream = new TwitchStream();

$raw_payload = file_get_contents('php://input');
$twitch_payload = json_decode($raw_payload, true);

// I NEED TO MAKE THIS FUNCTION
$processed_payload = $twitch_stream->processTwitchPayload($twitch_payload);

$discord_commenter = new DiscordCommenter($processed_payload);
$discord_commenter->run();













// THIS IS NOT IN THE RIGHT SPOT. MOVE IT TO ITS CORRECT SPOTS

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // if we get a challenge for our subscription via GET request

    $twitch_stream->verifyHubChallenge();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if we get a payload with stream info via POST request

    $raw_payload = file_get_contents('php://input');
    $payload = json_decode($raw_payload, true);

    $discord_commenter = new DiscordCommenter($payload);
    $discord_commenter->run();
}
