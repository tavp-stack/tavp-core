<?php

declare(strict_types=1);

namespace Tavp\Core\Templates;

/**
 * Defines the three starter tier templates (decision 10.9 / milestone TPL).
 *
 *  - website      : static pages, no database
 *  - application  : website + TAVPid auth + database
 *  - enterprise   : application + API + JWT + Docker + deploy configs
 */
abstract class TierTemplate
{
    abstract public function name(): string;

    abstract public function description(): string;

    /**
     * Return the list of files this template scaffolds.
     */
    abstract public function files(): array;

    /**
     * Whether this template includes the database layer.
     */
    public function usesDatabase(): bool
    {
        return false;
    }

    /**
     * Whether this template includes TAVPid authentication.
     */
    public function usesAuth(): bool
    {
        return false;
    }
}
