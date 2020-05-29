<?php

// This is a COMMAND LINE run script. You must provide a valid user id as an argument
// If you just provide the user id, it will subscribe. If you put unsubscribe as the second arg, you will
// unsubscribe to the user. 
//
// *** EXAMPLES ***
//
// To Subscribe to user 12345 stream: php Subscribe.php 12345
// To Unsubscribe to user 12345 stream: php Subscribe.php 12345 unsubscribe


require __DIR__ . '/' . 'Loader.php';

$twitch_stream = new TwitchStream();
$logger = new Logger();
$scipt_name = 'Subscribe.php';

$log_namespace = 'subscribe';

// array of valid user ids
$valid_user_ids = Secrets::VALID_USER_IDS;

if (count($argv) > 1) {
	if (in_array($argv[1], $valid_user_ids)) {
		$twitch_user_id = $argv[1];
		if ($argv[2] === 'unsubscribe') {
			$twitch_stream->subscribeToUser($twitch_user_id, true);
		} else {
	    	$twitch_stream->subscribeToUser($twitch_user_id);
		}	
	} else {
		$response = "{$argv[1]} is not a valid user id.";
		$failure_info = $this->logger->buildFailureLog($scipt_name, $log_namespace, $response);
		$this->logger->log_error($failure_info, $log_namespace);
	}
} else {
	$response = "Must provide a valid user id as script argument.";
	$failure_info = $this->logger->buildFailureLog($scipt_name, $log_namespace, $response);
	$this->logger->log_error($failure_info, $log_namespace);
}




