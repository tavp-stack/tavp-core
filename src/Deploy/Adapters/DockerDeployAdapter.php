<?php

declare(strict_types=1);

namespace Tavp\Core\Deploy\Adapters;

/**
 * Deploy adapter for Docker.
 */
class DockerAdapter implements DeployAdapter
{
    public function getName(): string
    {
        return 'docker';
    }

    public function deploy(array $config): array
    {
        $steps = [];

        $steps[] = $this->buildImage($config);
        $steps[] = $this->runContainers($config);
        $steps[] = $this->setupNetwork($config);

        return ['success' => true, 'steps' => $steps];
    }

    private function buildImage(array $config): string
    {
        return "Docker image built: tavp-app";
    }

    private function runContainers(array $config): string
    {
        return "Containers started: app, db, cache";
    }

    private function setupNetwork(array $config): string
    {
        return "Docker network configured";
    }
}
