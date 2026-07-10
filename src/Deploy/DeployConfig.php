<?php

declare(strict_types=1);

namespace Tavp\Core\Deploy;

/**
 * .tavp-deploy.yml configuration file handling.
 */
class DeployConfig
{
    private const DEFAULT_CONFIG = [
        'adapter' => 'docker',
        'domain' => 'localhost',
        'php_version' => '8.3',
        'db_name' => 'tavp',
        'db_user' => 'tavp_user',
        'docroot' => 'public',
        'optimize' => false,
        'pre_deploy' => [],
        'post_deploy' => [],
    ];

    public static function load(string $path): array
    {
        if (!file_exists($path)) {
            return self::DEFAULT_CONFIG;
        }

        $yaml = file_get_contents($path);

        return self::DEFAULT_CONFIG + self::parseYaml($yaml);
    }

    public static function save(string $path, array $config): bool
    {
        $yaml = self::toYaml($config + self::DEFAULT_CONFIG);
        return file_put_contents($path, $yaml) !== false;
    }

    private static function parseYaml(string $yaml): array
    {
        $result = [];
        $lines = explode("\n", $yaml);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            if (preg_match('/^(\w[\w-]*):\s*(.+)$/', $line, $matches)) {
                $key = $matches[1];
                $value = trim($matches[2]);

                if ($value === 'true') $value = true;
                elseif ($value === 'false') $value = false;
                elseif (is_numeric($value)) $value = (int) $value;

                $result[$key] = $value;
            }
        }

        return $result;
    }

    private static function toYaml(array $config): string
    {
        $lines = ['# TAVP Deploy Configuration'];

        foreach ($config as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $lines[] = "{$key}: {$value}";
        }

        return implode("\n", $lines) . "\n";
    }
}
