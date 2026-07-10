<?php

declare(strict_types=1);

namespace Tavp\Core\Coil;

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * TAVP Coil — Swoole-based runtime with coroutines.
 */
class CoilServer
{
    private Server $server;
    private string $host;
    private int $port;
    private int $workers;
    private int $coroutines;

    public function __construct(array $config)
    {
        $this->host = $config['host'] ?? '0.0.0.0';
        $this->port = $config['port'] ?? 9501;
        $this->workers = $config['workers'] ?? 4;
        $this->coroutines = $config['coroutines'] ?? 1000;
    }

    /**
     * Start the Swoole server.
     */
    public function start(): void
    {
        $this->server = new Server($this->host, $this->port);

        // Configure server
        $this->server->set([
            'worker_num' => $this->workers,
            'max_coroutine' => $this->coroutines,
            'open_http2_protocol' => true,
            'http_compression' => true,
            'compression_level' => 6,
            'enable_reactor_thread' => true,
            'hook_flags' => SWOOLE_HOOK_ALL,
        ]);

        // Register callbacks
        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('workerStart', [$this, 'onWorkerStart']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);

        echo "Starting TAVP Coil server on {$this->host}:{$this->port}\n";
        echo "Workers: {$this->workers} | Max Coroutines: {$this->coroutines}\n";

        $this->server->start();
    }

    /**
     * Handle server start.
     */
    public function onStart(Server $server): void
    {
        echo "TAVP Coil server started (PID: {$server->worker_pid})\n";
    }

    /**
     * Handle worker start.
     */
    public function onWorkerStart(Server $server, int $workerId): void
    {
        echo "Worker {$workerId} started\n";

        // Boot Phalcon once per worker
        $this->bootPhalcon();
    }

    /**
     * Handle HTTP request.
     */
    public function onRequest(Request $request, Response $response): void
    {
        try {
            // Convert Swoole request to Phalcon request
            $phalconRequest = $this->convertRequest($request);

            // Reset state between requests
            $this->requestCleanup();

            // Process request through Phalcon
            $app = $this->getApplication();
            $app->handle($phalconRequest);

            // Send response
            $response->status($app->response->getStatusCode());
            $response->header($app->response->getHeaders());
            $response->end($app->response->getContent());
        } catch (\Throwable $e) {
            $response->status(500);
            $response->end('Internal Server Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle async task.
     */
    public function onTask(Server $server, int $taskId, int $workerId, mixed $data): string
    {
        // Process background task
        return json_encode(['status' => 'completed', 'result' => $data]);
    }

    /**
     * Handle task completion.
     */
    public function onFinish(Server $server, int $taskId, string $data): void
    {
        echo "Task {$taskId} completed\n";
    }

    /**
     * Boot Phalcon framework once per worker.
     */
    private function bootPhalcon(): void
    {
        // Initialize Phalcon DI container
        // This stays in memory for the lifetime of the worker
    }

    /**
     * Reset state between requests (RequestCleanup).
     */
    private function requestCleanup(): void
    {
        // Clear static properties
        // Reset superglobals
        // Clear instantiated singletons
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_SERVER = [];
    }

    /**
     * Convert Swoole request to Phalcon-compatible request.
     */
    private function convertRequest(Request $request): object
    {
        return new class($request) {
            public function __construct(private Request $swooleRequest) {}

            public function getMethod(): string { return $this->swooleRequest->server['request_method'] ?? 'GET'; }
            public function getUri(): string { return $this->swooleRequest->server['request_uri'] ?? '/'; }
            public function getHeader(string $name): ?string { return $this->swooleRequest->header[strtolower($name)] ?? null; }
            public function getQuery(string $key = null): mixed { return $key ? ($this->swooleRequest->get[$key] ?? null) : $this->swooleRequest->get; }
            public function getPost(string $key = null): mixed { return $key ? ($this->swooleRequest->post[$key] ?? null) : $this->swooleRequest->post; }
            public function getJsonRawBody(): ?object { return json_decode($this->swooleRequest->rawContent()); }
        };
    }

    private function getApplication(): object
    {
        // Return Phalcon Application instance
        return new class {
            public object $response;
            public function handle($request): void { /* Handle request */ }
        };
    }
}
