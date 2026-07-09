<?php

declare(strict_types=1);

namespace Tavp\Core\Api;

/**
 * OpenAPI documentation generator.
 */
class OpenApiGenerator
{
    private array $paths = [];
    private array $schemas = [];

    /**
     * Generate OpenAPI documentation from routes.
     */
    public function generate(array $routes): array
    {
        $api = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => env('APP_NAME', 'TAVP API'),
                'version' => '1.0.0',
                'description' => 'API documentation generated from TAVP routes',
            ],
            'servers' => [
                ['url' => env('APP_URL', 'http://localhost'), 'description' => 'Development'],
            ],
            'paths' => $this->generatePaths($routes),
            'components' => [
                'schemas' => $this->schemas,
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ],
        ];

        return $api;
    }

    /**
     * Generate paths from route definitions.
     */
    private function generatePaths(array $routes): array
    {
        $paths = [];

        foreach ($routes as $route) {
            $path = $route['path'] ?? '/';
            $method = strtolower($route['method'] ?? 'get');
            $controller = $route['controller'] ?? '';
            $action = $route['action'] ?? 'index';

            $paths[$path][$method] = [
                'summary' => ucfirst($action) . ' ' . basename($path),
                'operationId' => $controller . '_' . $action,
                'tags' => [$this->getTag($path)],
                'responses' => [
                    '200' => ['description' => 'Success'],
                    '401' => ['description' => 'Unauthorized'],
                    '404' => ['description' => 'Not found'],
                ],
            ];

            // Add auth for protected routes
            if (str_starts_with($path, '/api/')) {
                $paths[$path][$method]['security'] = [['bearerAuth' => []]];
            }
        }

        return $paths;
    }

    /**
     * Get tag from path.
     */
    private function getTag(string $path): string
    {
        $parts = explode('/', trim($path, '/'));
        return ucfirst($parts[0] ?? 'default');
    }

    /**
     * Save OpenAPI spec to file.
     */
    public function save(array $routes, string $outputPath): void
    {
        $spec = $this->generate($routes);
        $json = json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($outputPath, $json);
    }

    /**
     * Get OpenAPI spec as JSON string.
     */
    public function toJson(array $routes): string
    {
        return json_encode($this->generate($routes), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
