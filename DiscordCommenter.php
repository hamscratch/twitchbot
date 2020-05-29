<?php

/*
 *
 */

class DiscordCommenter {

    const DISCORD_COMMENTER_NAMESPACE = 'discord_commenter';

    public $webhook_url;
    public $logger;

    public function __construct() {
        $this->webhook_url = Secrets::DISCORD_WEBHOOK_URL;
        $this->logger = new Logger();
    }

    /** 
     * Sends a payload to Discord webhook
     *
     * @param array $payload : contents of Discord webhook payload to send
     * @return bool true : on success of curl
     */
    public  function sendMessage(string $payload) : bool {
        $header = ['content-type: application/json'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        if ($result) {
            return json_decode($result, true);
        } else {
            $errno = curl_errno($ch);
            $error_message = curl_strerror($errno);
            $log_info = "cURL error ({$errno}): {$error_message}";
            $failure_info = $this->$logger->buildFailureLog(self::DISCORD_COMMENTER_NAMESPACE, 'sendMessage', $log_info);

            $this->logger->log_error($failure_info, self::DISCORD_COMMENTER_NAMESPACE);

            return false;
        }
    }
    
}