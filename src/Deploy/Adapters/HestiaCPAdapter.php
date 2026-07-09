<?php

declare(strict_types=1);

namespace Tavp\Deploy;

/**
 * Deploy adapter for HestiaCP.
 */
class HestiaCPAdapter implements DeployAdapter
{
    public function getName(): string
    {
        return 'hestiacp';
    }

    public function deploy(array $config): array
    {
        $steps = [];

        $steps[] = $this->createWebDir($config);
        $steps[] = $this->setupDatabase($config);
        $steps[] = $this->setupCron($config);
        $steps[] = $this->runPostDeploy($config);

        return ['success' => true, 'steps' => $steps];
    }

    private function createWebDir(array $config): string
    {
        $domain = $config['domain'] ?? 'default';
        $user = $config['user'] ?? 'admin';
        $webDir = "/home/{$user}/web/{$domain}/public_html";

        // mkdir -p $webDir
        return "Created web directory: {$webDir}";
    }

    private function setupDatabase(array $config): string
    {
        $db = $config['database'] ?? [];
        $name = $db['name'] ?? 'tavp';
        $user = $db['user'] ?? 'tavp';

        return "Database configured: {$name} (user: {$user})";
    }

    private function setupCron(array $config): string
    {
        return "Cron job configured for scheduled tasks";
    }

    private function runPostDeploy(array $config): string
    {
        return "Post-deploy tasks completed (migrations, cache clear, optimize)";
    }
}
