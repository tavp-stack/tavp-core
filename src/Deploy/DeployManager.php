<?php

declare(strict_types=1);

namespace Tavp\Core\Deploy;

/**
 * Manages deployment across different environments.
 */
class DeployManager
{
    private array $adapters = [];

    public function __construct()
    {
        $this->registerAdapter(new HestiaCPAdapter());
        $this->registerAdapter(new VpsAdapter());
        $this->registerAdapter(new DockerDeployAdapter());
    }

    public function registerAdapter(DeployAdapter $adapter): self
    {
        $this->adapters[$adapter->getName()] = $adapter;

        return $this;
    }

    public function deploy(string $adapterName, array $config): array
    {
        if (!isset($this->adapters[$adapterName])) {
            return ['success' => false, 'error' => "Unknown adapter: {$adapterName}"];
        }

        return $this->adapters[$adapterName]->deploy($config);
    }

    public function getAdapters(): array
    {
        return array_keys($this->adapters);
    }
}
