<?php

/* set up cron to run this with an argument of a USER_ID

IDs:
Pete = 122085265
Mike = 58761711

*/ 

$user_id = $argv1

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$challenge = $_GET["hub.challenge"];
	http_response_code(200);
	echo $challenge;
	exit();
}

/* subscribes to a given user's id. subscription lease set to 10 days.
must be renewed. 
*/ 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$url = "https://api.twitch.tv/helix/webhooks/hub";

	$data = [
		"hub.callback" => "http://34.71.198.211/discord_commenter.php",
		"hub.mode" => "subscribe",
		"hub.topic" => "https://api.twitch.tv/helix/streams?user_id={$user_id}",
		"hub.lease_seconds" => "864000",
		];

	$payload = json_encode($data);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	$result = curl_exec($ch);

	if ($result) {
		return json_decode($result, true);
	} else {
		return false;
	}
}
