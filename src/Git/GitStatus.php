<?php

declare(strict_types=1);

namespace Tavp\Core\Git;

/**
 * Detects Git repository state and provides status information.
 */
class GitStatus
{
    /**
     * Check if current directory is inside a Git repository.
     */
    public static function isRepository(string $path): bool
    {
        $result = exec("git -C " . escapeshellarg($path) . " rev-parse --is-inside-work-tree 2>&1");
        return $result === 'true';
    }

    /**
     * Get the current branch name.
     */
    public static function getCurrentBranch(string $path): string
    {
        return (string) exec("git -C " . escapeshellarg($path) . " rev-parse --abbrev-ref HEAD");
    }

    /**
     * Get Git status output.
     */
    public static function getStatus(string $path): array
    {
        $output = [];
        exec("git -C " . escapeshellarg($path) . " status --porcelain", $output);
        return $output;
    }

    /**
     * Check if there are uncommitted changes.
     */
    public static function hasChanges(string $path): bool
    {
        $status = self::getStatus($path);
        return !empty($status);
    }

    /**
     * Get list of configured remotes.
     */
    public static function getRemotes(string $path): array
    {
        $output = [];
        exec("git -C " . escapeshellarg($path) . " remote -v", $output);
        return $output;
    }

    /**
     * Check if a remote exists.
     */
    public static function hasRemote(string $path, string $remote): bool
    {
        $remotes = self::getRemotes($path);
        foreach ($remotes as $line) {
            if (strpos($line, $remote . "\t") === 0 || strpos($line, $remote . ' ') === 0) {
                return true;
            }
        }
        return false;
    }
}
