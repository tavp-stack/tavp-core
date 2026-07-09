<?php

declare(strict_types=1);

namespace Tavp\Core\Log;

/**
 * Structured logger — JSON formatted logs with request ID correlation.
 */
class Logger
{
    private string $channel;
    private string $requestId;
    private array $context = [];
    private float $startTime;

    public function __construct(string $channel = 'app')
    {
        $this->channel = $channel;
        $this->requestId = $this->generateRequestId();
        $this->startTime = microtime(true);
    }

    /**
     * Log an info message.
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log a warning message.
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log an error message.
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log a debug message.
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Log a critical message.
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Add context to all subsequent log entries.
     */
    public function pushContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }

    /**
     * Get the request ID.
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * Get elapsed time since logger creation.
     */
    public function getElapsedTime(): float
    {
        return round((microtime(true) - $this->startTime) * 1000, 2);
    }

    private function log(string $level, string $message, array $context): void
    {
        $entry = [
            'timestamp' => date('c'),
            'level' => $level,
            'channel' => $this->channel,
            'message' => $message,
            'request_id' => $this->requestId,
            'elapsed_ms' => $this->getElapsedTime(),
            'context' => array_merge($this->context, $context),
        ];

        $json = json_encode($entry, JSON_UNESCAPED_UNICODE);
        $this->write($level, $json);
    }

    private function write(string $level, string $json): void
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $json . "\n", FILE_APPEND | LOCK_EX);

        if ($level === 'error' || $level === 'critical') {
            $errorFile = $logDir . '/errors-' . date('Y-m-d') . '.log';
            file_put_contents($errorFile, $json . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    private function generateRequestId(): string
    {
        return bin2hex(random_bytes(8));
    }
}
