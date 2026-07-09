<?php

declare(strict_types=1);

namespace Tavp\Core\Health;

/**
 * Health check endpoint — verify all services are operational.
 */
class HealthCheck
{
    private array $checks = [];
    private array $results = [];

    /**
     * Register a health check.
     */
    public function check(string $name, callable $callback): void
    {
        $this->checks[$name] = $callback;
    }

    /**
     * Run all health checks.
     */
    public function run(): array
    {
        $this->results = [];
        $healthy = true;

        foreach ($this->checks as $name => $callback) {
            $start = microtime(true);

            try {
                $result = $callback();
                $status = $result ? 'pass' : 'fail';
            } catch (\Throwable $e) {
                $result = $e->getMessage();
                $status = 'fail';
                $healthy = false;
            }

            $this->results[$name] = [
                'status' => $status,
                'message' => is_string($result) ? $result : ($status === 'pass' ? 'OK' : 'Failed'),
                'time_ms' => round((microtime(true) - $start) * 1000, 2),
            ];

            if ($status === 'fail') {
                $healthy = false;
            }
        }

        return [
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => date('c'),
            'checks' => $this->results,
        ];
    }

    /**
     * Get check results as JSON.
     */
    public function toJson(): string
    {
        $results = $this->run();
        http_response_code($results['status'] === 'healthy' ? 200 : 503);
        header('Content-Type: application/json');
        return json_encode($results, JSON_PRETTY_PRINT);
    }

    /**
     * Get check results as HTTP response.
     */
    public function respond(): void
    {
        $results = $this->run();
        http_response_code($results['status'] === 'healthy' ? 200 : 503);
        header('Content-Type: application/json');
        echo json_encode($results, JSON_PRETTY_PRINT);
    }
}
