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

	public function buildFailureLog(string $class, $call_name, $response = null) : string {
		$empty_string_response = '';

		$log_data = [
			'timestamp' => date('c'),
			'class' => $class,
			'failed_call' => $call_name
		];

		$log_data['response'] = $response ?? $empty_string_response;

		$failure_info = json_encode($log_data, true);

		return $failure_info;

	}

	private function log(string $message, string $path) {
		$log_base = '/var/log/apache2/';
		$log_path = $log_base . $path;

		$log_message = $message . "\n";	

		error_log($log_message, 3, $log_path);
	}
}