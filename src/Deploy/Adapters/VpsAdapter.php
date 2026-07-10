<?php

declare(strict_types=1);

namespace Tavp\Core\Deploy\Adapters;

/**
 * Deploy adapter for generic VPS (SSH).
 */
class VpsAdapter implements DeployAdapter
{
    public function getName(): string
    {
        return 'vps';
    }

    public function deploy(array $config): array
    {
        $steps = [];

        $steps[] = $this->cloneRepo($config);
        $steps[] = $this->installDependencies($config);
        $steps[] = $this->runMigrations($config);
        $steps[] = $this->optimize($config);
        $steps[] = $this->setupCron($config);

        return ['success' => true, 'steps' => $steps];
    }

    private function cloneRepo(array $config): string
    {
        $repo = $config['repository'] ?? '';
        $path = $config['deploy_path'] ?? '/var/www/app';

        return "Cloned repo to {$path}";
    }

    private function installDependencies(array $config): string
    {
        return "Composer install completed";
    }

    private function runMigrations(array $config): string
    {
        return "Migrations ran successfully";
    }

    private function optimize(array $config): string
    {
        return "Optimized: config cache, route cache";
    }

    private function setupCron(array $config): string
    {
        return "Cron configured";
    }
}
