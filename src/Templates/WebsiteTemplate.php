<?php

declare(strict_types=1);

namespace Tavp\Core\Templates;

/**
 * The Website tier: a brochure-style site with pages, no database.
 */
class WebsiteTemplate extends TierTemplate
{
    public function name(): string
    {
        return 'website';
    }

    public function description(): string
    {
        return 'A simple website with pages. No database required.';
    }

    public function files(): array
    {
        return [
            'composer.json',
            'package.json',
            '.env.example',
            'routes/web.php',
            'resources/views/home.volt',
        ];
    }
}
