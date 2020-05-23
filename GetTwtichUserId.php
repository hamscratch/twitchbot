<?php 

// Simple script to help you find out a twitch user's id number by providing the username
// Call the script and provide the username as $argv[1]

require __DIR__ . '/' . 'Loader.php';

$twitch_client_id = Secrets::TWITCH_CLIENT_ID;
$twitch_auth_token = Secrets::TWITCH_AUTH_TOKEN;

$url = 'https://api.twitch.tv/helix/users?login=' . $argv[1];
$headers = ["Client-ID: {$twitch_client_id}", "Authorization: Bearer {$twitch_auth_token}"];

$ch = curl_init();

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$result = curl_exec($ch);

$user_info = json_decode($result, true);

$user_name = $user_info['data'][0]['display_name'];
$user_id = $user_info['data'][0]['id'];

echo "Username: {$user_name}\nUserId: {$user_id} \n";