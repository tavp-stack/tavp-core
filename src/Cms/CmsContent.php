<?php

declare(strict_types=1);

namespace Tavp\Core\Cms;

/**
 * Lightweight CMS content model.
 *
 * Stores editable blocks and collections so non-programmers can manage
 * page content from the admin panel (0.5.0 CMS module).
 */
class CmsContent
{
    private array $blocks = [];
    private array $collections = [];

    /**
     * Set a named content block (used by cms_block() in Volt).
     */
    public function setBlock(string $key, string $value): void
    {
        $this->blocks[$key] = $value;
    }

    public function getBlock(string $key, string $default = ''): string
    {
        return $this->blocks[$key] ?? $default;
    }

    /**
     * Add an item to a named collection (e.g. "testimonials").
     */
    public function addToCollection(string $name, array $item): void
    {
        $this->collections[$name][] = $item;
    }

    public function getCollection(string $name): array
    {
        return $this->collections[$name] ?? [];
    }
}
