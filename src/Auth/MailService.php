<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Mail service — sends via SMTP (works with Mailpit in dev).
 */
class MailService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(string $to, string $subject, string $body, string $html = ''): bool
    {
        $host = $this->config['host'] ?? '127.0.0.1';
        $port = (int) ($this->config['port'] ?? 1025);
        $from = $this->config['from'] ?? 'noreply@example.com';

        $fp = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$fp) {
            return false;
        }

        // Read server greeting
        fgets($fp, 512);

        // EHLO
        fwrite($fp, "EHLO tavp\r\n");
        $this->readAll($fp);

        // MAIL FROM
        fwrite($fp, "MAIL FROM:<{$from}>\r\n");
        $this->readAll($fp);

        // RCPT TO
        fwrite($fp, "RCPT TO:<{$to}>\r\n");
        $this->readAll($fp);

        // DATA
        fwrite($fp, "DATA\r\n");
        $this->readAll($fp);

        // Build email
        $email = "From: {$from}\r\n";
        $email .= "To: {$to}\r\n";
        $email .= "Subject: {$subject}\r\n";
        $email .= "MIME-Version: 1.0\r\n";

        if (!empty($html)) {
            $boundary = md5(uniqid((string) time()));
            $email .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $email .= "\r\n--{$boundary}\r\n";
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $email .= $body . "\r\n";
            $email .= "--{$boundary}\r\n";
            $email .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $email .= $html . "\r\n";
            $email .= "--{$boundary}--\r\n";
        } else {
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $email .= $body . "\r\n";
        }

        fwrite($fp, $email);
        fwrite($fp, ".\r\n");
        $this->readAll($fp);

        fwrite($fp, "QUIT\r\n");
        fclose($fp);

        return true;
    }

    private function readAll($fp): void
    {
        while (!feof($fp)) {
            $line = fgets($fp, 512);
            if ($line === false || strlen($line) < 4) {
                break;
            }
            // 250 or 354 = ok, anything else might be error
            if (strpos($line, '250') === 0 || strpos($line, '354') === 0) {
                break;
            }
        }
    }
}
