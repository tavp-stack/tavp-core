<?php

declare(strict_types=1);

namespace Tavp\Core\View;

use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;
use Phalcon\Mvc\ViewBaseInterface;

/**
 * TAVP's Volt templating engine wrapper.
 *
 * Volt is Phalcon's native template language (compiled to plain PHP,
 * so it is fast). This class registers TAVP-specific helpers and
 * configures safe compilation (auto-escaping on by default).
 *
 * Note: this is Phalcon Volt, NOT Laravel Volt (Livewire). They share
 * a name but are completely different technologies.
 */
class VoltEngine
{
    private PhalconVolt $volt;

    public function __construct(ViewBaseInterface $view, mixed $di = null)
    {
        $this->volt = new PhalconVolt($view, $di);

        $this->volt->setOptions([
            // Compile templates into the storage/compiled directory.
            'compiledPath' => storage_path('compiled/volt/'),
            'compiledSeparator' => '_',
            // Always recompile when the source changes.
            'compileAlways' => env('APP_ENV', 'local') === 'local',
            // Escape HTML output automatically — security by default.
            'autoescape' => true,
        ]);

        $this->registerHelpers();
    }

    /**
     * Expose TAVP helpers inside Volt templates.
     */
    private function registerHelpers(): void
    {
        $compiler = $this->volt->getCompiler();

        // asset() — build a URL to a public asset.
        $compiler->addFunction('asset', function ($path) {
            return 'echo asset(' . $path . ');';
        });

        // route() — generate a URL for a named route.
        $compiler->addFunction('route', function ($name, $params = '[]') {
            return 'echo route(' . $name . ', ' . $params . ');';
        });

        // cms_block() / cms_collection() — reserved for the CMS module.
        $compiler->addFunction('cms_block', function ($key) {
            return 'echo cms_block(' . $key . ');';
        });
    }

    public function getVolt(): PhalconVolt
    {
        return $this->volt;
    }
}
