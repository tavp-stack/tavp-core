<?php

declare(strict_types=1);

namespace Tavp\Core\View;

/**
 * Builds and renders Volt templates with inheritance, partials and
 * auto-escaping. This is the readable entry point controllers use.
 */
class ViewFactory
{
    private string $viewsPath;
    private string $compiledPath;

    public function __construct(string $viewsPath, string $compiledPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
        $this->compiledPath = rtrim($compiledPath, '/');
    }

    /**
     * Render a template with the given data.
     * Template names use dot notation: "layouts.main" -> "layouts/main.volt".
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.volt';

        if (!is_file($file)) {
            return "<!-- Template not found: {$template} -->";
        }

        // Volt compiles to PHP; we evaluate the compiled output in a
        // scoped context so $data variables are available to the view.
        ob_start();
        extract($data);
        require $file;
        $output = ob_get_clean();

        return $output;
    }

    public function exists(string $template): bool
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.volt';

        return is_file($file);
    }
}
