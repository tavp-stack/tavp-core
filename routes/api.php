<?php

// API routes for TAVP Core.

use Tavp\Core\Controllers\Api\AuthApiController;
use Tavp\Core\Http\Middleware\ThrottleRequests;
use Tavp\Core\Routing\Router;

/** @var Router $router */

$router->group([
    'prefix' => '/api/v1',
    'middleware' => [ThrottleRequests::class],
], function (Router $router) {

    // Authentication
    $router->post('/auth/send-otp', [AuthApiController::class, 'sendOtp'])->name('api.auth.send-otp');
    $router->post('/auth/verify-otp', [AuthApiController::class, 'verifyOtp'])->name('api.auth.verify-otp');
    $router->post('/auth/refresh', [AuthApiController::class, 'refresh'])->name('api.auth.refresh');

    // Health check
    $router->get('/health', function () {
        return json_encode([
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => '0.1.0',
        ]);
    })->name('api.health');
});
