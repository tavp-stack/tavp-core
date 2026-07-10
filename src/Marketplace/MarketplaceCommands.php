<?php

declare(strict_types=1);

namespace Tavp\Core\Marketplace;

/**
 * CLI commands for marketplace operations.
 */
class MarketplaceCommands
{
    public function __construct(
        private ModuleMarketplace $modules,
        private ThemeMarketplace $themes
    ) {
    }

    /**
     * tavp marketplace:search <query>
     */
    public function search(string $query): void
    {
        echo "Searching for: {$query}\n\n";

        $modules = $this->modules->search($query);
        $themes = $this->themes->search($query);

        if (!empty($modules)) {
            echo "Modules:\n";
            foreach ($modules as $module) {
                echo "  - {$module['name']} ({$module['slug']}) - {$module['description']}\n";
            }
        }

        if (!empty($themes)) {
            echo "\nThemes:\n";
            foreach ($themes as $theme) {
                echo "  - {$theme['name']} ({$theme['slug']}) - {$theme['description']}\n";
            }
        }
    }

    /**
     * tavp marketplace:install <slug>
     */
    public function install(string $slug): void
    {
        echo "Installing: {$slug}\n";

        $module = $this->modules->get($slug);

        if (!empty($module)) {
            if ($this->modules->install($slug)) {
                echo "✓ Module installed successfully!\n";
            } else {
                echo "✗ Failed to install module.\n";
            }
        } else {
            $theme = $this->themes->get($slug);
            if (!empty($theme)) {
                if ($this->themes->install($slug)) {
                    echo "✓ Theme installed successfully!\n";
                } else {
                    echo "✗ Failed to install theme.\n";
                }
            } else {
                echo "✗ Module or theme not found: {$slug}\n";
            }
        }
    }

    /**
     * tavp marketplace:publish <type> <path>
     */
    public function publish(string $type, string $path): void
    {
        echo "Publishing {$type} from: {$path}\n";

        $data = $this->readPackage($path);

        if ($type === 'module') {
            $result = $this->modules->publish($data);
        } else {
            $result = $this->themes->publish($data);
        }

        if (isset($result['slug'])) {
            echo "✓ Published successfully! Slug: {$result['slug']}\n";
        } else {
            echo "✗ Failed to publish.\n";
        }
    }

    private function readPackage(string $path): array
    {
        $composerJson = $path . '/composer.json';
        if (file_exists($composerJson)) {
            return json_decode(file_get_contents($composerJson), true);
        }
        return [];
    }
}
