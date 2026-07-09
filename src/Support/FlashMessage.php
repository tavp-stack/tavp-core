<?php

declare(strict_types=1);

namespace Tavp\Core\Flash;

/**
 * Flash message service — store messages for next request.
 */
class FlashMessage
{
    private array $messages = [];
    private const TYPES = ['success', 'error', 'warning', 'info'];

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
    }

    /**
     * Add a flash message.
     */
    public function add(string $type, string $message, string $key = 'default'): void
    {
        if (!in_array($type, self::TYPES)) {
            $type = 'info';
        }

        $_SESSION['_flash'][$type][$key] = $message;
    }

    /**
     * Add success message.
     */
    public function success(string $message, string $key = 'default'): void
    {
        $this->add('success', $message, $key);
    }

    /**
     * Add error message.
     */
    public function error(string $message, string $key = 'default'): void
    {
        $this->add('error', $message, $key);
    }

    /**
     * Add warning message.
     */
    public function warning(string $message, string $key = 'default'): void
    {
        $this->add('warning', $message, $key);
    }

    /**
     * Add info message.
     */
    public function info(string $message, string $key = 'default'): void
    {
        $this->add('info', $message, $key);
    }

    /**
     * Get all messages of a specific type.
     */
    public function get(string $type): array
    {
        return $_SESSION['_flash'][$type] ?? [];
    }

    /**
     * Get all flash messages.
     */
    public function all(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $messages;
    }

    /**
     * Check if there are messages of a specific type.
     */
    public function has(string $type): bool
    {
        return !empty($_SESSION['_flash'][$type]);
    }

    /**
     * Clear all flash messages.
     */
    public function clear(): void
    {
        $_SESSION['_flash'] = [];
    }
}
