<?php

declare(strict_types=1);

// Public entry point for TAVP Core.
// All web requests are routed through this file.

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Support/helpers.php';

use Tavp\Core\Application;
use Tavp\Core\Kernel;
use Tavp\Core\Routing\Router;

$app = new Application(dirname(__DIR__));
$app->bootstrap();

$router = new Router();
require_once $app->getBasePath() . '/routes/web.php';

$kernel = new Kernel($app, $router);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

echo $kernel->handle($method, $uri);
