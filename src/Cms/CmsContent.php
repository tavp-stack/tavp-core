<?php

declare(strict_types=1);

namespace Tavp\Core\Cms;

/**
 * CMS content model — lightweight content management primitives.
 *
 * Provides blocks, collections, SEO, taxonomy relations, and revision
 * tracking so non-programmers can manage page content from the admin panel.
 *
 * This is the core-level helper; the full CMS module (tavp-cms) provides
 * the storage drivers, BREAD, and admin UI on top of this.
 */
class CmsContent
{
    private array $blocks = [];
    private array $collections = [];
    private array $seo = [];
    private array $taxonomy = [];
    private array $metadata = [];

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
     * Get all blocks.
     *
     * @return array<string,string>
     */
    public function blocks(): array
    {
        return $this->blocks;
    }

    /**
     * Check if a block exists and is non-empty.
     */
    public function hasBlock(string $key): bool
    {
        return isset($this->blocks[$key]) && $this->blocks[$key] !== '';
    }

    /**
     * Add an item to a named collection (e.g. "testimonials").
     */
    public function addToCollection(string $name, array $item): void
    {
        $this->collections[$name][] = $item;
    }

    /**
     * Get all items in a collection.
     *
     * @return array<int,array<string,mixed>>
     */
    public function getCollection(string $name): array
    {
        return $this->collections[$name] ?? [];
    }

    /**
     * Get collection count.
     */
    public function collectionCount(string $name): int
    {
        return count($this->collections[$name] ?? []);
    }

    /**
     * Check if a collection has items.
     */
    public function hasCollection(string $name): bool
    {
        return !empty($this->collections[$name]);
    }

    /**
     * Get all collection names.
     *
     * @return string[]
     */
    public function collectionNames(): array
    {
        return array_keys($this->collections);
    }

    // --- SEO -----------------------------------------------------------------

    /**
     * Set SEO meta fields.
     *
     * @param array{title?:string,description?:string,image?:string,url?:string,type?:string,locale?:string,site_name?:string,robots?:string} $seo
     */
    public function setSeo(array $seo): void
    {
        $this->seo = array_merge($this->seo, $seo);
    }

    /**
     * Get a single SEO field.
     */
    public function seo(string $key, string $default = ''): string
    {
        return (string) ($this->seo[$key] ?? $default);
    }

    /**
     * Get all SEO data.
     *
     * @return array<string,string>
     */
    public function seoData(): array
    {
        return $this->seo;
    }

    /**
     * Generate Open Graph meta tags.
     */
    public function openGraphTags(): string
    {
        $tags = [];

        if (!empty($this->seo['title'])) {
            $tags[] = '<meta property="og:title" content="' . htmlspecialchars($this->seo['title']) . '">';
        }
        if (!empty($this->seo['description'])) {
            $tags[] = '<meta property="og:description" content="' . htmlspecialchars($this->seo['description']) . '">';
        }
        if (!empty($this->seo['image'])) {
            $tags[] = '<meta property="og:image" content="' . htmlspecialchars($this->seo['image']) . '">';
        }
        if (!empty($this->seo['url'])) {
            $tags[] = '<meta property="og:url" content="' . htmlspecialchars($this->seo['url']) . '">';
        }
        $tags[] = '<meta property="og:type" content="' . htmlspecialchars($this->seo['type'] ?? 'website') . '">';
        if (!empty($this->seo['site_name'])) {
            $tags[] = '<meta property="og:site_name" content="' . htmlspecialchars($this->seo['site_name']) . '">';
        }

        return implode("\n    ", $tags);
    }

    /**
     * Generate Twitter Card meta tags.
     */
    public function twitterCardTags(): string
    {
        $tags = ['<meta name="twitter:card" content="summary_large_image">'];

        if (!empty($this->seo['title'])) {
            $tags[] = '<meta name="twitter:title" content="' . htmlspecialchars($this->seo['title']) . '">';
        }
        if (!empty($this->seo['description'])) {
            $tags[] = '<meta name="twitter:description" content="' . htmlspecialchars($this->seo['description']) . '">';
        }
        if (!empty($this->seo['image'])) {
            $tags[] = '<meta name="twitter:image" content="' . htmlspecialchars($this->seo['image']) . '">';
        }

        return implode("\n    ", $tags);
    }

    // --- Taxonomy ------------------------------------------------------------

    /**
     * Attach taxonomy terms to this content.
     *
     * @param string[] $termIds
     */
    public function setTaxonomy(string $type, array $termIds): void
    {
        $this->taxonomy[$type] = $termIds;
    }

    /**
     * Get taxonomy term IDs for a type.
     *
     * @return string[]
     */
    public function getTaxonomy(string $type): array
    {
        return $this->taxonomy[$type] ?? [];
    }

    /**
     * Check if this content has a specific taxonomy term.
     */
    public function hasTerm(string $taxonomyType, string $termId): bool
    {
        return in_array($termId, $this->taxonomy[$taxonomyType] ?? [], true);
    }

    /**
     * Get all taxonomy types assigned.
     *
     * @return array<string,string[]>
     */
    public function allTaxonomy(): array
    {
        return $this->taxonomy;
    }

    // --- Metadata ------------------------------------------------------------

    /**
     * Set arbitrary metadata (author, timestamps, etc.).
     */
    public function setMeta(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function meta(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * @return array<string,mixed>
     */
    public function allMeta(): array
    {
        return $this->metadata;
    }

    // --- Serialization -------------------------------------------------------

    /**
     * Export all content data as an array (for storage).
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'blocks' => $this->blocks,
            'collections' => $this->collections,
            'seo' => $this->seo,
            'taxonomy' => $this->taxonomy,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Rehydrate from a stored array.
     *
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $content = new self();
        $content->blocks = $data['blocks'] ?? [];
        $content->collections = $data['collections'] ?? [];
        $content->seo = $data['seo'] ?? [];
        $content->taxonomy = $data['taxonomy'] ?? [];
        $content->metadata = $data['metadata'] ?? [];

        return $content;
    }

    /**
     * Merge content from an external array (e.g. from database row).
     *
     * @param array<string,mixed> $data
     */
    public function merge(array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->collections[$key] = array_merge($this->collections[$key] ?? [], $value);
            } elseif (is_string($value)) {
                $this->blocks[$key] = $value;
            }
        }
    }
}
