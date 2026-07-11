<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Mail service abstraction — supports SMTP, Mailgun, SES.
 */
class MailService
{
    private string $driver;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->driver = $config['driver'] ?? 'smtp';
    }

    /**
     * Send an email.
     */
    public function send(string $to, string $subject, string $body, string $html = ''): bool
    {
        return match ($this->driver) {
            'smtp' => $this->sendSmtp($to, $subject, $body, $html),
            'mailgun' => $this->sendMailgun($to, $subject, $body),
            'ses' => $this->sendSes($to, $subject, $body),
            'log' => $this->sendLog($to, $subject, $body),
            default => false,
        };
    }

    private function sendSmtp(string $to, string $subject, string $body, string $html = ''): bool
    {
        $host = $this->config['host'] ?? 'smtp.example.com';
        $port = $this->config['port'] ?? 587;
        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';
        $from = $this->config['from'] ?? 'noreply@example.com';

        $errno = 0;
        $errstr = '';
        $fp = fsockopen($host, $port, $errno, $errstr, 30);

        if (!$fp) {
            return false;
        }

        $response = fgets($fp, 512);
        fwrite($fp, "EHLO tavp\r\n");
        $response = fgets($fp, 512);

        if ($username !== '') {
            fwrite($fp, "AUTH LOGIN\r\n");
            $response = fgets($fp, 512);
            fwrite($fp, base64_encode($username) . "\r\n");
            $response = fgets($fp, 512);
            fwrite($fp, base64_encode($password) . "\r\n");
            $response = fgets($fp, 512);
        }

        fwrite($fp, "MAIL FROM:<{$from}>\r\n");
        $response = fgets($fp, 512);
        fwrite($fp, "RCPT TO:<{$to}>\r\n");
        $response = fgets($fp, 512);
        fwrite($fp, "DATA\r\n");
        $response = fgets($fp, 512);

        $headers = "From: {$from}\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";

        if (!empty($html)) {
            $boundary = md5(uniqid((string) time()));
            $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n";
            $headers .= "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n";
            $headers .= $body . "\r\n--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
            $headers .= $html . "\r\n--{$boundary}--\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $headers .= $body . "\r\n";
        }

        fwrite($fp, $headers . ".\r\n");
        $response = fgets($fp, 512);
        fwrite($fp, "QUIT\r\n");
        fclose($fp);

        return true;
    }

    private function sendMailgun(string $to, string $subject, string $body, array $attachments): bool
    {
        $apiKey = $this->config['api_key'] ?? '';
        $domain = $this->config['domain'] ?? '';
        $from = $this->config['from'] ?? "noreply@{$domain}";

        $url = "https://api.mailgun.net/v3/{$domain}/messages";

        $data = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'text' => $body,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "api:{$apiKey}",
            CURLOPT_POSTFIELDS => $data,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    private function sendSes(string $to, string $subject, string $body, array $attachments): bool
    {
        // AWS SES via SDK
        if (!class_exists('Aws\Ses\SesClient')) {
            return false;
        }

        $client = new \Aws\Ses\SesClient([
            'region' => $this->config['region'] ?? 'us-east-1',
            'version' => '2010-12-01',
            'credentials' => [
                'key' => $this->config['access_key'] ?? '',
                'secret' => $this->config['secret_key'] ?? '',
            ],
        ]);

        try {
            $client->sendEmail([
                'Source' => $this->config['from'] ?? 'noreply@example.com',
                'Destination' => ['ToAddresses' => [$to]],
                'Message' => [
                    'Subject' => ['Data' => $subject],
                    'Body' => ['Text' => ['Data' => $body]],
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function sendLog(string $to, string $subject, string $body): bool
    {
        $logDir = storage_path('logs/mail');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        $entry = date('Y-m-d H:i:s') . " - To: {$to} | Subject: {$subject}\n{$body}\n\n";

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

        return true;
    }
}
