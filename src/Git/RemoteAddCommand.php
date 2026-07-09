<?php

declare(strict_types=1);

namespace Tavp\Core\Git;

/**
 * Add a Git remote to the repository.
 */
class RemoteAddCommand
{
    public function __construct(private GitStatus $git)
    {
    }

    /**
     * Add a remote to the repository.
     */
    public function execute(string $path, string $name, string $url): bool
    {
        if (!GitStatus::isRepository($path)) {
            throw new \RuntimeException("Not a Git repository: {$path}");
        }

        if (GitStatus::hasRemote($path, $name)) {
            throw new \RuntimeException("Remote '{$name}' already exists");
        }

        $escapedPath = escapeshellarg($path);
        $escapedName = escapeshellarg($name);
        $escapedUrl = escapeshellarg($url);

        exec("git -C {$escapedPath} remote add {$escapedName} {$escapedUrl} 2>&1", $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException("Failed to add remote: " . implode("\n", $output));
        }

        return true;
    }
}
