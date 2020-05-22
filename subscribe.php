<?php

require __DIR__ . '/' . 'Loader.php';

$twitch_stream = new TwitchStream();
$twitch_logger = new TwitchLogger();

// array of valid user ids
$valid_user_ids = Secrets::VALID_USER_IDS;

if ( ! is_null($argv)) {
	if (count($argv) > 1) {
		if (in_array($argv[1], $valid_user_ids)) {
			$twitch_user_id = $argv[1];
	    	$twitch_stream->subscribeToUser($twitch_user_id);
		} else {
			$message = "{$argv[1]} is not a valid user id."
			$twitch_logger->log_error($message);
		}
	} else {
		$message = "Must provide a valid user id as script argument."
		$twitch_logger->log_error($message);
	}
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$challenge = $_GET['hub_challenge'];
	$twitch_stream->verifyHubChallenge($challenge);
}



