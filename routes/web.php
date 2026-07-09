<?php

// Default web routes for a fresh TAVP Core project.

use Tavp\Core\Routing\Router;

/** @var Router $router */

$router->get('/', function () {
    return 'Welcome to TAVP Core — Tailwind + Alpine + Volt + Phalcon.';
});

$router->get('/about', function () {
    return 'TAVP is a fast, ergonomic PHP stack built on Phalcon.';
});

$router->get('/contact', function () {
    return 'Reach the TAVP team through the official channels.';
});
