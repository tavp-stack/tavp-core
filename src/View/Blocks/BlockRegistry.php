<?php

declare(strict_types=1);

namespace Tavp\Core\View\Blocks;

/**
 * Registry of reusable UI components (TAVPblocks).
 *
 * Each block is a Volt macro name paired with default options. The
 * frontend ships 15 core blocks in 0.2.0, expanding to 40+ in 0.4.0.
 *
 * Blocks: Button, Input, Select, Textarea, Toggle, Modal, Dropdown,
 * Toast, Card, Badge, Avatar, Datatable, Pagination, Alert, Skeleton.
 */
class BlockRegistry
{
    private array $blocks = [
        'Button', 'Input', 'Select', 'Textarea', 'Toggle',
        'Modal', 'Dropdown', 'Toast', 'Card', 'Badge',
        'Avatar', 'Datatable', 'Pagination', 'Alert', 'Skeleton',
    ];

    /**
     * Return all registered block names.
     */
    public function all(): array
    {
        return $this->blocks;
    }

    /**
     * Check whether a block exists.
     */
    public function has(string $name): bool
    {
        return in_array($name, $this->blocks, true);
    }

    /**
     * Register an additional block (used by modules).
     */
    public function register(string $name): void
    {
        if (!$this->has($name)) {
            $this->blocks[] = $name;
        }
    }
}
