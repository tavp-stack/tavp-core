<?php

declare(strict_types=1);

namespace Tavp\Theme;

/**
 * Theme manager — filesystem-based theme system.
 */
class ThemeManager
{
    private string $themesPath;
    private string $currentTheme;

    public function __construct(string $themesPath = '', string $currentTheme = 'default')
    {
        $this->themesPath = $themesPath ?: base_path('resources/themes');
        $this->currentTheme = $currentTheme;
    }

    /**
     * Get all available themes.
     */
    public function getThemes(): array
    {
        $themes = [];
        $dirs = glob($this->themesPath . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $name = basename($dir);
            $configFile = $dir . '/theme.json';

            $themes[$name] = [
                'name' => $name,
                'path' => $dir,
                'config' => file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [],
            ];
        }

        return $themes;
    }

    /**
     * Get current theme.
     */
    public function getCurrentTheme(): string
    {
        return $this->currentTheme;
    }

    /**
     * Set current theme.
     */
    public function setCurrentTheme(string $theme): void
    {
        $this->currentTheme = $theme;
    }

    /**
     * Get theme asset URL.
     */
    public function asset(string $path): string
    {
        return "/themes/{$this->currentTheme}/{$path}";
    }

    /**
     * Get theme view path.
     */
    public function view(string $view): string
    {
        $themePath = $this->themesPath . "/{$this->currentTheme}/views/{$view}.volt";

        if (file_exists($themePath)) {
            return $themePath;
        }

        return base_path("resources/views/{$view}.volt");
    }

    /**
     * Get theme option.
     */
    public function option(string $key, mixed $default = null): mixed
    {
        $configFile = $this->themesPath . "/{$this->currentTheme}/theme.json";

        if (!file_exists($configFile)) {
            return $default;
        }

        $config = json_decode(file_get_contents($configFile), true);
        return $config[$key] ?? $default;
    }

    /**
     * Get all theme options.
     */
    public function options(): array
    {
        $configFile = $this->themesPath . "/{$this->currentTheme}/theme.json";

        if (!file_exists($configFile)) {
            return [];
        }

        return json_decode(file_get_contents($configFile), true) ?? [];
    }
}
