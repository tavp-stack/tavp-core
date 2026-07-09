<?php

declare(strict_types=1);

// Public entry point for TAVP Core.
// All web requests are routed through this file.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Support/helpers.php';

use Tavp\Core\Application;
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
