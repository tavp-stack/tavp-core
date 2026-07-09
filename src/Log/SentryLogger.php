<?php

declare(strict_types=1);

namespace Tavp\Core\Log;

/**
 * Sentry integration — send errors to Sentry for tracking.
 */
class SentryLogger
{
    private string $dsn;
    private bool $enabled;

    public function __construct(array $config)
    {
        $this->dsn = $config['dsn'] ?? '';
        $this->enabled = !empty($this->dsn);
    }

    /**
     * Report an exception to Sentry.
     */
    public function report(\Throwable $exception, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $payload = [
            'level' => 'error',
            'message' => $exception->getMessage(),
            'exception' => [
                'type' => get_class($exception),
                'value' => $exception->getMessage(),
                'stacktrace' => [
                    'frames' => $this->getStackTrace($exception),
                ],
            ],
            'tags' => $context['tags'] ?? [],
            'extra' => $context['extra'] ?? [],
        ];

        $this->send($payload);
    }

    /**
     * Report a message to Sentry.
     */
    public function message(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $payload = [
            'level' => $level,
            'message' => $message,
            'tags' => $context['tags'] ?? [],
            'extra' => $context['extra'] ?? [],
        ];

        $this->send($payload);
    }

    private function send(array $payload): void
    {
        $headers = [
            'Content-Type: application/json',
            'X-Sentry-Auth: Sentry sentry_version=7, sentry_key=' . $this->getKey(),
        ];

        $ch = curl_init($this->dsn);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    private function getKey(): string
    {
        // Extract key from DSN
        if (preg_match('#https://[^:]+:([^@]+)@#', $this->dsn, $matches)) {
            return $matches[1];
        }
        return '';
    }

    private function getStackTrace(\Throwable $exception): array
    {
        $trace = $exception->getTrace();
        $frames = [];

        foreach ($trace as $frame) {
            $frames[] = [
                'filename' => $frame['file'] ?? '',
                'lineno' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? '',
                'class' => $frame['class'] ?? '',
            ];
        }

        return $frames;
    }
}
