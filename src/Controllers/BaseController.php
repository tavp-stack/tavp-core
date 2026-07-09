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
     * Render a Volt view template (implemented in the Volt layer).
     * For now it returns a simple HTML string as a placeholder.
     */
    protected function view(string $template, array $data = []): string
    {
        // The Volt integration (Fase 1, VLT) will compile and render
        // the template. Until then we return a readable placeholder.
        $dataString = http_build_query($data);

        return sprintf(
            '<!-- View: %s -->' . "\n" . '<p>Rendered with data: %s</p>',
            htmlspecialchars($template),
            htmlspecialchars($dataString)
        );
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
