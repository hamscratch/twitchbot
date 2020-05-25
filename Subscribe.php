<?php

// This is a COMMAND LINE run script. You must provide a valid user id as an argument

require __DIR__ . '/' . 'Loader.php';

$twitch_stream = new TwitchStream();
$logger = new Logger();
$log_namespace = 'subscribe';

// array of valid user ids
$valid_user_ids = Secrets::VALID_USER_IDS;

if (count($argv) > 1) {
	if (in_array($argv[1], $valid_user_ids)) {
		$message = "This is what argv[2] is... {$argv[2]}";
		$log_namespace->log_error($message, $log_namespace);
		$twitch_user_id = $argv[1];
		if ($argv[2] === true) {
			$twitch_stream->subscribeToUser($twitch_user_id, true);
		} else {
	    	$twitch_stream->subscribeToUser($twitch_user_id);
		}	
	} else {
		$message = "{$argv[1]} is not a valid user id.";
		$logger->log_error($message, $log_namespace);
	}
} else {
	$message = "Must provide a valid user id as script argument.";
	$logger->log_error($message, $log_namespace);
}




