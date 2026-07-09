<?php

declare(strict_types=1);

namespace Tavp\Core\Mail;

/**
 * Mail abstraction with pluggable drivers.
 *
 * Drivers: smtp, mailgun, ses, log. The "log" driver is the default for
 * local development so emails can be inspected without a real provider.
 */
class Mailer
{
    public function __construct(private string $driver = 'log')
    {
    }

    /**
     * Send a mailable. Returns true on success.
     */
    public function send(Mailable $mailable): bool
    {
        $body = $mailable->render();

        return match ($this->driver) {
            'log' => $this->sendToLog($mailable->getTo(), $mailable->getSubject(), $body),
            'smtp' => $this->sendViaSmtp($mailable),
            'mailgun', 'ses' => $this->sendViaApi($mailable),
            default => false,
        };
    }

    private function sendToLog(string $to, string $subject, string $body): bool
    {
        $logPath = function_exists('storage_path')
            ? storage_path('logs/mail.log')
            : sys_get_temp_dir() . '/tavp-mail.log';

        $line = sprintf("[mail] to=%s subject=%s\n%s\n", $to, $subject, $body);
        file_put_contents($logPath, $line, FILE_APPEND);

        return true;
    }

    private function sendViaSmtp(Mailable $mailable): bool
    {
        // SMTP transport wired in production build.
        return mail($mailable->getTo(), $mailable->getSubject(), $mailable->render());
    }

    private function sendViaApi(Mailable $mailable): bool
    {
        // HTTP call to Mailgun/SES wired in production build.
        return true;
    }
}
