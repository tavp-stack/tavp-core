<?php

declare(strict_types=1);

namespace Tavp\Core\Environment\Adapters;

/**
 * DDEV environment adapter — generates .ddev/config.yaml.
 */
class DdevAdapter
{
    public function getName(): string
    {
        return 'ddev';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['.ddev/config.yaml'] = $this->configYaml($config);

        return $files;
    }

    private function configYaml(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';
        $dbName = $config['db_name'] ?? 'tavp';
        $docroot = $config['docroot'] ?? 'public';

        return <<<YAML
name: tavp-app
type: php
docroot: {$docroot}
php_version: "{$phpVersion}"
php_type: apache
webserver: apache

database:
  type: mariadb
  version: "11.0"

hooks:
  post-start:
    - exec: composer install
    - exec: npm install

config:
  web-environment:
    - APP_ENV=local
    - APP_DEBUG=true
    - DB_CONNECTION=mysql
    - DB_HOST=db
    - DB_DATABASE={$dbName}
    - DB_USERNAME=db
    - DB_PASSWORD=db
    - DB_PORT=3306
YAML;
    }
}
