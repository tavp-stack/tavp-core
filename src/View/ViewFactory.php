<?php

declare(strict_types=1);

namespace Tavp\Core\View;

use Phalcon\Mvc\View\Engine\Volt\Compiler;

/**
 * Builds and renders Volt templates with inheritance, partials and
 * auto-escaping. This is the readable entry point controllers use.
 *
 * Templates are compiled to plain PHP by Phalcon's Volt compiler (fast),
 * cached in the compiled path, and evaluated with the given data.
 */
class ViewFactory
{
    private string $viewsPath;
    private string $compiledPath;
    private ?Compiler $compiler = null;

    public function __construct(string $viewsPath, string $compiledPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
        $this->compiledPath = rtrim($compiledPath, '/');
    }

    /**
     * Render a template with the given data.
     * Template names use dot notation: "layouts.main" -> "layouts/main.volt".
     *
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $relative = str_replace('.', '/', $template) . '.volt';
        $file = $this->viewsPath . '/' . $relative;

        if (!is_file($file)) {
            return "<!-- Template not found: {$template} -->";
        }

        $compiled = $this->compile($relative);

        ob_start();
        extract($data, EXTR_SKIP);
        include $compiled;

        return (string) ob_get_clean();
    }

    public function exists(string $template): bool
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.volt';

        return is_file($file);
    }

    /**
     * Compile a Volt template to PHP and return the compiled file path.
     *
     * Compilation runs with the views directory as the working directory so
     * that {% extends %} and {% include %} paths resolve relative to it.
     */
    private function compile(string $relative): string
    {
        if (!is_dir($this->compiledPath)) {
            mkdir($this->compiledPath, 0755, true);
        }

        $compiler = $this->compiler();

        $cwd = getcwd();
        chdir($this->viewsPath);

        try {
            $compiler->compile($relative);

            return $compiler->getCompiledTemplatePath();
        } finally {
            if ($cwd !== false) {
                chdir($cwd);
            }
        }
    }

    private function compiler(): Compiler
    {
        if ($this->compiler !== null) {
            return $this->compiler;
        }

        $compiler = new Compiler();
        $compiler->setOptions([
            'always' => (string) env('APP_ENV', 'production') === 'local',
            'extension' => '.php',
            'separator' => '_',
            'path' => $this->compiledPath . '/',
            'prefix' => '',
        ]);

        $this->registerFunctions($compiler);

        return $this->compiler = $compiler;
    }

    /**
     * Expose TAVP helpers inside Volt templates.
     */
    private function registerFunctions(Compiler $compiler): void
    {
        $compiler->addFunction('asset', fn ($path) => 'asset(' . $path . ')');
        $compiler->addFunction('url', fn ($path) => 'url(' . $path . ')');
        $compiler->addFunction('route', fn ($name, $params = '[]') => 'route(' . $name . ', ' . $params . ')');
        $compiler->addFunction('csrf_token', fn () => 'csrf_token()');
        $compiler->addFunction('config', fn ($key, $default = 'null') => 'config(' . $key . ', ' . $default . ')');
    }
}
