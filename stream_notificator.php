<?php


$url = 'https://api.twitch.tv/helix/webhooks/hub';


$payload = [
			'hub.callback' => 'http://34.71.198.211/',
			'hub.mode' => 'subscribe',
			'hub.topic' => 'https://api.twitch.tv/helix/streams?user_id=<ID>',
			'hub.lease_seconds' => '60',
			'hub.secret' => '',
		],

	
$payload_string = http_build_query($payload);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);

$result = curl_exec($ch);

