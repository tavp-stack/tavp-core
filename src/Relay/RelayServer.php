<?php

declare(strict_types=1);

namespace Tavp\Core\Relay;

use Spiral\RoadRunner\Worker;
use Spiral\RoadRunner\PSR7Client;

/**
 * TAVP Relay — RoadRunner-based runtime with process isolation.
 */
class RelayServer
{
    private Worker $worker;
    private PSR7Client $psr7;
    private string $host;
    private int $port;
    private int $workers;

    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? '0.0.0.0';
        $this->port = $config['port'] ?? 8081;
        $this->workers = $config['workers'] ?? 4;
    }

    /**
     * Start the RoadRunner worker.
     */
    public function start(): void
    {
        // Create RoadRunner worker
        $this->worker = Worker::create();
        $this->psr7 = new PSR7Client($this->worker);

        echo "Starting TAVP Relay worker...\n";

        while ($this->worker->waitDispatch()) {
            try {
                $psrRequest = $this->psr7->receiveRequest();

                // Process request through TAVP
                $response = $this->handleRequest($psrRequest);

                $this->psr7->respond($response);
            } catch (\Throwable $e) {
                $this->worker->error($e->getMessage());
            }
        }
    }

    /**
     * Handle an incoming request.
     */
    private function handleRequest(object $request): object
    {
        // Convert PSR-7 request to TAVP request
        // Process through application
        // Return PSR-7 response

        return new class {
            public int $statusCode = 200;
            public array $headers = ['Content-Type' => ['text/html']];
            public string $body = 'Hello from TAVP Relay!';
        };
    }

    /**
     * Get worker status.
     */
    public function getStatus(): array
    {
        return [
            'runtime' => 'roadrunner',
            'pid' => getmypid(),
            'memory' => memory_get_usage(true),
            'uptime' => time() - $_SERVER['REQUEST_TIME'] ?? 0,
        ];
    }
}
