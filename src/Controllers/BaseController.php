<?php

declare(strict_types=1);

namespace Tavp\Core\Controllers;

use Tavp\Core\Http\Request;
use Tavp\Core\Http\Response;

/**
 * The base controller every web controller should extend.
 *
 * Provides ergonomic helpers: view(), json(), redirect(), back().
 * Keeping these here means child controllers stay short and readable.
 */
abstract class BaseController
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Render a Volt view template using the global view() helper.
     */
    protected function view(string $template, array $data = []): string
    {
        return \view($template, $data);
    }

    /**
     * Return a JSON response.
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        return (new Response())->json($data, $status);
    }

    /**
     * Redirect to another path or named route.
     */
    protected function redirect(string $path, int $status = 302): Response
    {
        return (new Response())
            ->header('Location', $path)
            ->setStatusCode($status);
    }

    /**
     * Redirect back to the previous page.
     */
    protected function back(int $status = 302): Response
    {
        $previous = $_SERVER['HTTP_REFERER'] ?? '/';

        return $this->redirect($previous, $status);
    }
}
