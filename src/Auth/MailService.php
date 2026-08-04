<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * Multi-driver mail service.
 *
 * Supported drivers:
 * - smtp     : Raw SMTP socket (default, works with Mailpit in dev)
 * - mailgun  : Mailgun API
 * - sendgrid : SendGrid API
 * - ses      : Amazon SES API
 * - postmark : Postmark API
 * - gmail    : Gmail SMTP (via PHPMailer)
 *
 * Config keys:
 *   driver, host, port, username, password, encryption, from,
 *   mailgun_domain, mailgun_secret,
 *   sendgrid_api_key,
 *   ses_key, ses_secret, ses_region,
 *   postmark_token
 */
class MailService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Send an email using the configured driver.
     *
     * @throws \RuntimeException If sending fails
     */
    public function send(string $to, string $subject, string $body, string $html = ''): bool
    {
        $driver = $this->config['driver'] ?? 'smtp';

        return match ($driver) {
            'smtp'     => $this->sendViaSmtp($to, $subject, $body, $html),
            'mailgun'  => $this->sendViaMailgun($to, $subject, $body, $html),
            'sendgrid' => $this->sendViaSendgrid($to, $subject, $body, $html),
            'ses'      => $this->sendViaSes($to, $subject, $body, $html),
            'postmark' => $this->sendViaPostmark($to, $subject, $body, $html),
            'gmail'    => $this->sendViaGmail($to, $subject, $body, $html),
            default    => throw new \RuntimeException("Unsupported mail driver: {$driver}"),
        };
    }

    // -------------------------------------------------------------------
    //  SMTP (raw socket — works with Mailpit in dev)
    // -------------------------------------------------------------------

    private function sendViaSmtp(string $to, string $subject, string $body, string $html): bool
    {
        $host = $this->config['host'] ?? '127.0.0.1';
        $port = (int) ($this->config['port'] ?? 1025);
        $from = $this->config['from'] ?? 'noreply@example.com';
        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';

        // Port 465 = implicit TLS: connect through an SSL context from the start.
        // Port 587 = plain, then upgrade via STARTTLS.
        $secure = $port === 465;

        if ($secure) {
            $ctx = stream_context_create([
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);
            $fp = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $ctx
            );
        } else {
            $fp = @fsockopen($host, $port, $errno, $errstr, 10);
        }

        if (!$fp) {
            throw new \RuntimeException("SMTP connection failed: {$errstr} ({$errno})");
        }

        // Read server greeting
        $this->readAll($fp, ['220']);

        // EHLO
        fwrite($fp, "EHLO tavp\r\n");
        $this->readAll($fp);

        // STARTTLS if port 587
        if ($port === 587) {
            fwrite($fp, "STARTTLS\r\n");
            $this->readAll($fp, ['220']);
            stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
            fwrite($fp, "EHLO tavp\r\n");
            $this->readAll($fp);
        }

        // AUTH LOGIN if credentials provided
        if ($username !== '' && $password !== '') {
            fwrite($fp, "AUTH LOGIN\r\n");
            $this->readAll($fp, ['334']);
            fwrite($fp, base64_encode($username) . "\r\n");
            $this->readAll($fp, ['334']);
            fwrite($fp, base64_encode($password) . "\r\n");
            $this->readAll($fp, ['235']);
        }

        // MAIL FROM
        fwrite($fp, "MAIL FROM:<{$from}>\r\n");
        $this->readAll($fp);

        // RCPT TO
        fwrite($fp, "RCPT TO:<{$to}>\r\n");
        $this->readAll($fp);

        // DATA
        fwrite($fp, "DATA\r\n");
        $this->readAll($fp, ['354']);

        // Build email
        $messageId = time() . '.' . bin2hex(random_bytes(6)) . '@' . (preg_replace('/^[^@]*@/', '', $from) ?: 'tavp.web.id');
        $email = "Message-ID: <{$messageId}>\r\n";
        $email .= "Date: " . date('r') . "\r\n";
        $email .= "From: {$from}\r\n";
        $email .= "To: {$to}\r\n";
        $email .= "Subject: {$subject}\r\n";
        $email .= "MIME-Version: 1.0\r\n";
        $email .= "X-Mailer: TAVP CMS Mailer\r\n";

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

    /**
     * Consume a (possibly multiline) SMTP response and verify its code.
     *
     * Multi-line replies are signalled by "NNN-..." lines; the final line is
     * "NNN <text>". Throws if the returned code is not in $expected.
     */
    private function readAll($fp, array $expected = ['250']): string
    {
        $last = '';
        while (!feof($fp)) {
            $line = fgets($fp, 512);
            if ($line === false || strlen($line) < 4) {
                break;
            }
            $last = trim($line);
            // Final line of a reply: "NNN text"
            if ($line[3] === ' ') {
                break;
            }
            // Continuation "NNN-...": keep reading
        }

        if ($last === '') {
            throw new \RuntimeException('SMTP: empty or closed reply');
        }

        $code = substr($last, 0, 3);
        if (!in_array($code, $expected, true)) {
            throw new \RuntimeException("SMTP server error: {$last}");
        }

        return $last;
    }

    // -------------------------------------------------------------------
    //  Mailgun
    // -------------------------------------------------------------------

    private function sendViaMailgun(string $to, string $subject, string $body, string $html): bool
    {
        if (!class_exists(\Mailgun\Mailgun::class)) {
            throw new \RuntimeException('Mailgun SDK not installed. Run: composer require mailgun/mailgun-php');
        }

        $domain = $this->config['mailgun_domain'] ?? '';
        $secret = $this->config['mailgun_secret'] ?? '';
        $from   = $this->config['from'] ?? 'noreply@example.com';

        if ($domain === '' || $secret === '') {
            throw new \RuntimeException('Mailgun domain and secret are required');
        }

        $mg = \Mailgun\Mailgun::create($secret);
        $messageBuilder = $mg->messages()->getDefaultMessageBuilder();

        $messageBuilder->setFromAddress($from);
        $messageBuilder->addToRecipient($to);
        $messageBuilder->setSubject($subject);
        $messageBuilder->setTextBody($body);

        if ($html !== '') {
            $messageBuilder->setHtmlBody($html);
        }

        $response = $mg->messages()->send($domain, $messageBuilder);

        return $response->getId() !== '';
    }

    // -------------------------------------------------------------------
    //  SendGrid
    // -------------------------------------------------------------------

    private function sendViaSendgrid(string $to, string $subject, string $body, string $html): bool
    {
        if (!class_exists(\SendGrid::class)) {
            throw new \RuntimeException('SendGrid SDK not installed. Run: composer require sendgrid/sendgrid');
        }

        $apiKey = $this->config['sendgrid_api_key'] ?? '';
        $from   = $this->config['from'] ?? 'noreply@example.com';

        if ($apiKey === '') {
            throw new \RuntimeException('SendGrid API key is required');
        }

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->addContent('text/plain', $body);

        if ($html !== '') {
            $email->addContent('text/html', $html);
        }

        $sg = new \SendGrid($apiKey);
        $response = $sg->send($email);

        return $response->statusCode() >= 200 && $response->statusCode() < 300;
    }

    // -------------------------------------------------------------------
    //  Amazon SES
    // -------------------------------------------------------------------

    private function sendViaSes(string $to, string $subject, string $body, string $html): bool
    {
        if (!class_exists(\Aws\Ses\SesClient::class)) {
            throw new \RuntimeException('AWS SDK not installed. Run: composer require aws/aws-sdk-php');
        }

        $key    = $this->config['ses_key'] ?? '';
        $secret = $this->config['ses_secret'] ?? '';
        $region = $this->config['ses_region'] ?? 'us-east-1';
        $from   = $this->config['from'] ?? 'noreply@example.com';

        if ($key === '' || $secret === '') {
            throw new \RuntimeException('Amazon SES key and secret are required');
        }

        $ses = new \Aws\Ses\SesClient([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret,
            ],
        ]);

        $params = [
            'Source' => $from,
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Message' => [
                'Subject' => [
                    'Data' => $subject,
                    'Charset' => 'UTF-8',
                ],
                'Body' => [
                    'Text' => [
                        'Data' => $body,
                        'Charset' => 'UTF-8',
                    ],
                ],
            ],
        ];

        if ($html !== '') {
            $params['Message']['Body']['Html'] = [
                'Data' => $html,
                'Charset' => 'UTF-8',
            ];
        }

        $result = $ses->sendEmail($params);

        return $result->get('MessageId') !== '';
    }

    // -------------------------------------------------------------------
    //  Postmark
    // -------------------------------------------------------------------

    private function sendViaPostmark(string $to, string $subject, string $body, string $html): bool
    {
        if (!class_exists(\Postmark\PostmarkClient::class)) {
            throw new \RuntimeException('Postmark SDK not installed. Run: composer require wildbit/postmark-php');
        }

        $token = $this->config['postmark_token'] ?? '';
        $from  = $this->config['from'] ?? 'noreply@example.com';

        if ($token === '') {
            throw new \RuntimeException('Postmark server token is required');
        }

        $client = new \Postmark\PostmarkClient($token);

        if ($html !== '') {
            $response = $client->sendEmail(
                $from,
                $to,
                $subject,
                $html,
                null,
                null,
                null,
                null,
                null,
                null,
                $body
            );
        } else {
            $response = $client->sendEmail(
                $from,
                $to,
                $subject,
                $body
            );
        }

        return $response->getErrorCode() === 0;
    }

    // -------------------------------------------------------------------
    //  Gmail (via PHPMailer)
    // -------------------------------------------------------------------

    private function sendViaGmail(string $to, string $subject, string $body, string $html): bool
    {
        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            throw new \RuntimeException('PHPMailer not installed. Run: composer require phpmailer/phpmailer');
        }

        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';
        $from     = $this->config['from'] ?? $username;

        if ($username === '' || $password === '') {
            throw new \RuntimeException('Gmail username and app password are required');
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($from);
        $mail->addAddress($to);

        $mail->isHTML($html !== '');
        $mail->Subject = $subject;
        $mail->Body    = $html !== '' ? $html : $body;

        if ($html !== '') {
            $mail->AltBody = $body;
        }

        $mail->send();

        return true;
    }

    // -------------------------------------------------------------------
    //  Helper: Get available drivers
    // -------------------------------------------------------------------

    /**
     * Get list of available drivers based on installed packages.
     *
     * @return array<string, bool>
     */
    public static function getAvailableDrivers(): array
    {
        return [
            'smtp'     => true,
            'mailgun'  => class_exists(\Mailgun\Mailgun::class),
            'sendgrid' => class_exists(\SendGrid::class),
            'ses'      => class_exists(\Aws\Ses\SesClient::class),
            'postmark' => class_exists(\Postmark\PostmarkClient::class),
            'gmail'    => class_exists(\PHPMailer\PHPMailer\PHPMailer::class),
        ];
    }
}
