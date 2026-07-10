<?php

declare(strict_types=1);

namespace Tavp\Core;

use Tavp\Core\Config\ConfigLoader;
use Tavp\Core\Environment\EnvironmentDetector;
use Tavp\Core\Environment\EnvironmentLoader;

/**
 * The main application entry point.
 *
 * Responsibilities:
 *  - hold the project root path
 *  - load .env variables and configuration files
 *  - act as a small dependency-injection container for shared services
 *  - detect the current environment
 *
 * This is intentionally framework-light: under the hood it relies on
 * Phalcon's C-extension speed, but the public surface is ergonomic.
 */
class Application
{
    private static ?self $instance = null;

    private string $basePath;
    private array $env = [];
    private ConfigLoader $config;
    private EnvironmentDetector $environmentDetector;
    private string $environment = 'local';
    private array $services = [];

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->config = new ConfigLoader();
        $this->environmentDetector = new EnvironmentDetector();

        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Application has not been bootstrapped yet.');
        }

        return self::$instance;
    }

    /**
     * Boot the application: load env, config, and detect environment.
     */
    public function bootstrap(): self
    {
        $this->loadEnvironment();
        $this->config->loadDirectory($this->basePath . '/config');
        $this->environment = $this->environmentDetector->detect($this->env['APP_ENV'] ?? null);

        return $this;
    }

    private function loadEnvironment(): void
    {
        $loader = new EnvironmentLoader();
        $this->env = $loader->load($this->basePath . '/.env');

        // Real environment variables take precedence over the .env file.
        foreach (getenv() as $key => $value) {
            $this->env[$key] = $value;
        }
    }

    public function getEnv(string $key, mixed $default = null): mixed
    {
        return $this->env[$key] ?? $default;
    }

    public function getConfig(): ConfigLoader
    {
        return $this->config;
    }

    /**
     * Convenience accessor for a config value using "file.key" dot notation.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Register a shared service (singleton) in the container.
     */
    public function bind(string $name, callable $factory): void
    {
        $this->services[$name] = $factory;
    }

    /**
     * Resolve a previously bound service, creating it once and caching it.
     */
    public function getService(string $name): mixed
    {
        if (!isset($this->services[$name])) {
            throw new \RuntimeException("Service '{$name}' is not registered.");
        }

        $factory = $this->services[$name];

        // Cache the resolved instance for the lifetime of the request.
        if (is_callable($factory) && !isset($this->resolved[$name])) {
            $this->resolved[$name] = $factory($this);
        }

        return $this->resolved[$name] ?? $factory($this);
    }

    private array $resolved = [];
}
