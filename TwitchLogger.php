<?php


class Logger {
	const TWITCHBOT_INFO_LOG = '/var/log/apache2/twitchbot_info.log';
	const TWITCHBOT_DEBUG_LOG = '/var/log/apache2/twitchbot_debug.log';
	const TWITCHBOT_ERROR_LOG = '/var/log/apache2/twitchbot_error.log';

	public function log_info(string $message) {
		$this->log($message, self::TWITCHBOT_INFO_LOG);
	}

	public function log_debug(string $message) {
		$this->log($message, self::TWITCHBOT_DEBUG_LOG);
	}

	public function log_error(string $message) {
		$this->log($message, self::TWITCHBOT_ERROR_LOG);
	}

	private function log(string $message, string $log_path) {
		error_log($message, 3, $log_path);
	}
}