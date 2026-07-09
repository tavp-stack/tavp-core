<?php

declare(strict_types=1);

namespace Tavp\Core\Infrastructure;

/**
 * Queue abstraction for background jobs.
 *
 * The database driver stores jobs in a table; Redis driver is added in
 * production. Workers run via "tavp queue:work".
 */
class Queue
{
    private array $jobs = [];

    /**
     * Push a job (callable or serializable payload) onto the queue.
     */
    public function push(callable|array $job): void
    {
        $this->jobs[] = $job;
    }

    /**
     * Run all pending jobs (used by the worker).
     */
    public function work(): int
    {
        $count = 0;
        while ($job = array_shift($this->jobs)) {
            is_callable($job) ? $job() : $this->handleArray($job);
            $count++;
        }

        return $count;
    }

    private function handleArray(array $job): void
    {
        // Dispatch to a Job class: ['class' => 'SendEmail', 'payload' => [...]]
        if (isset($job['class']) && class_exists($job['class'])) {
            (new $job['class']())->handle($job['payload'] ?? []);
        }
    }
}
