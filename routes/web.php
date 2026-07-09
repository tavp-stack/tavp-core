<?php

// Default web routes for a fresh TAVP Core project.

use Tavp\Core\Controllers\PageController;
use Tavp\Core\Routing\Router;

/** @var Router $router */

$router->get('/', [PageController::class, 'home'])->name('home');
$router->get('/about', [PageController::class, 'about'])->name('about');
$router->get('/contact', [PageController::class, 'contact'])->name('contact');

// Dashboard (requires authentication — use AuthMiddleware when ready)
$router->get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
