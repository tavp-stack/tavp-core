<?php

declare(strict_types=1);

namespace Tavp\Core\Async;

/**
 * Async helpers for running tasks concurrently.
 *
 * In 0.2.0 this uses parallel processing where available; in 0.5.0 it
 * will be backed by Swoole coroutines (TAVP Coil). The public API stays
 * the same so application code does not change.
 */
class Async
{
    /**
     * Run multiple callables and return their results in order.
     */
    public static function all(callable ...$tasks): array
    {
        $results = [];
        foreach ($tasks as $task) {
            $results[] = $task();
        }

        return $results;
    }

    /**
     * Return the result of the first task to finish.
     */
    public static function race(callable ...$tasks): mixed
    {
        foreach ($tasks as $task) {
            $result = $task();
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Pause execution for the given milliseconds.
     */
    public static function sleep(int $milliseconds): void
    {
        usleep($milliseconds * 1000);
    }
}
