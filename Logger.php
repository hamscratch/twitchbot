<?php


class Logger {

	public function log_info(string $message, string $class) {
		$path = $class . '_info.log';
		$this->log($message, $path);
	}

	public function log_debug(string $message, string $class) {
		$path = $class . '_debug.log';
		$this->log($message, $path);
	}

	public function log_error(string $message, string $class) {
		$path = $class . '_error.log';
		$this->log($message, $path);
	}

	private function log(string $message, string $path) {
		$log_base = '/var/log/apache2/';
		$log_path = $log_base . $path;

		error_log($message, 3, $log_path);
	}
}