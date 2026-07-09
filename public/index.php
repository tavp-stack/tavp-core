<?php

declare(strict_types=1);

// Public entry point for TAVP Core.
// All web requests are routed through this file.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Support/helpers.php';

use Tavp\Core\Application;
use Tavp\Core\Auth\AuthService;
use Tavp\Core\Auth\OtpService;
use Tavp\Core\Auth\TokenService;
use Tavp\Core\Config\ConfigLoader;
use Tavp\Core\Kernel;
use Tavp\Core\Routing\Router;
use Tavp\Core\View\ViewFactory;

$app = new Application(dirname(__DIR__));
$app->bootstrap();

// Register shared services
$app->bind('router', fn () => $router);

$app->bind('view', fn () => new ViewFactory(
    $app->getBasePath() . '/resources/views',
    storage_path('compiled/volt')
));

$app->bind('config', fn () => $app->getConfig());

$app->bind('tokens', fn () => new TokenService(
    env('JWT_SECRET', 'tavp-jwt-secret-change-me'),
    (int) env('JWT_ACCESS_TTL', 15),
    (int) env('JWT_REFRESH_TTL', 30)
));

$app->bind('otp', fn () => new OtpService(
    (int) env('OTP_LENGTH', 6),
    (int) env('OTP_TTL_MINUTES', 5),
    (int) env('OTP_MAX_ATTEMPTS', 5)
));

$app->bind('auth', fn () => new AuthService(
    app('tokens'),
    app('otp')
));

$router = new Router();

// Load routes
$isApi = str_starts_with($_SERVER['REQUEST_URI'] ?? '/', '/api');

if ($isApi) {
    require_once $app->getBasePath() . '/routes/api.php';
} else {
    require_once $app->getBasePath() . '/routes/web.php';
    require_once $app->getBasePath() . '/routes/api.php';
}

$kernel = new Kernel($app, $router);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

echo $kernel->handle($method, $uri);
