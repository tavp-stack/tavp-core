<?php

declare(strict_types=1);

namespace Tavp\Core\Environment;

use Tavp\Core\Environment\Adapters\LandoAdapter;

/**
 * Detects available environment tooling and recommends the best adapter.
 *
 * Scoring (higher = better fit):
 *   - Lando: 100 (Docker-based, cross-platform, founder's choice)
 *   - Docker: 80
 *   - Native: 50 (requires Phalcon already installed)
 */
class EnvironmentManager
{
    private array $adapters = [];

    public function __construct()
    {
        $this->adapters['lando'] = new LandoAdapter();
    }

    /**
     * Detect which adapters are present and return them scored.
     */
    public function detect(): array
    {
        $found = [];

        if ($this->adapters['lando']->isAvailable()) {
            $found['lando'] = 100;
        }
        if (is_file('docker-compose.yml')) {
            $found['docker'] = 80;
        }
        if ($this->phalconInstalled()) {
            $found['native'] = 50;
        }

        arsort($found);

        return $found;
    }

    /**
     * Write the chosen environment into .tavp.json metadata.
     */
    public function writeChoice(string $env, string $projectName): void
    {
        $meta = [
            'name' => $projectName,
            'env' => $env,
            'created' => date('Y-m-d'),
        ];

        file_put_contents('.tavp.json', json_encode($meta, JSON_PRETTY_PRINT));
    }

    private function phalconInstalled(): bool
    {
        return extension_loaded('phalcon');
    }
}
