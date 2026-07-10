<?php

declare(strict_types=1);

namespace Tavp\Core\Deploy;

/**
 * Contract for deploy adapters.
 */
interface DeployAdapter
{
    public function getName(): string;

    /**
     * Execute the deployment.
     *
     * @return array{success: bool, steps: array}
     */
    public function deploy(array $config): array;
}
