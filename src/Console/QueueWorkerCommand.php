<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp queue:work — process jobs from the queue.
 *
 * Usage: tavp queue:work [--queue=default] [--tries=3] [--timeout=60]
 */
class QueueWorkerCommand
{
    public function handle(array $args): void
    {
        $queue = 'default';
        $tries = 3;
        $timeout = 60;

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--queue=')) {
                $queue = substr($arg, 8);
            }
            if (str_starts_with($arg, '--tries=')) {
                $tries = (int) substr($arg, 8);
            }
            if (str_starts_with($arg, '--timeout=')) {
                $timeout = (int) substr($arg, 10);
            }
        }

        echo "Processing queue: {$queue}\n";
        echo "Max tries: {$tries}, Timeout: {$timeout}s\n\n";

        $running = true;

        pcntl_signal(SIGTERM, function () use (&$running) {
            $running = false;
            echo "\nShutting down gracefully...\n";
        });

        pcntl_signal(SIGINT, function () use (&$running) {
            $running = false;
            echo "\nShutting down gracefully...\n";
        });

        while ($running) {
            pcntl_signal_dispatch();

            try {
                $queueManager = app('queue');
                $job = $queueManager->pop($queue);

                if ($job === null) {
                    sleep(1);
                    continue;
                }

                $this->processJob($job, $queueManager, $queue);
            } catch (\Throwable $e) {
                echo "Error: {$e->getMessage()}\n";
                sleep(1);
            }
        }
    }

    private function processJob(object $job, $queueManager, string $queue): void
    {
        $jobClass = $job->job_class ?? $job->job ?? null;
        $start = microtime(true);

        echo "  Processing: {$jobClass} (ID: {$job->id})\n";

        try {
            if (!class_exists($jobClass)) {
                throw new \RuntimeException("Job class not found: {$jobClass}");
            }

            $instance = new $jobClass();
            $payload = json_decode($job->payload ?? '{}', true);

            if (method_exists($instance, 'handle')) {
                $instance->handle($payload);
            }

            $elapsed = round((microtime(true) - $start) * 1000);
            echo "    Done in {$elapsed}ms\n";

            if (method_exists($queueManager, 'delete')) {
                $queueManager->delete($job);
            }
        } catch (\Throwable $e) {
            $attempts = ($job->attempts ?? 0) + 1;
            echo "    Failed (attempt {$attempts}): {$e->getMessage()}\n";

            if ($attempts >= ($job->max_tries ?? 3)) {
                if (method_exists($queueManager, 'failed')) {
                    $queueManager->failed($job, $e->getMessage());
                }
                echo "    Job marked as failed.\n";
            } else {
                if (method_exists($queueManager, 'release')) {
                    $queueManager->release($job);
                }
                echo "    Released back to queue.\n";
            }
        }
    }
}
