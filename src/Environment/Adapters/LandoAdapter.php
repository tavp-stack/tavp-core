<?php

declare(strict_types=1);

namespace Tavp\Core\Environment\Adapters;

/**
 * Lando environment adapter (Docker under the hood).
 *
 * Generates a .lando.yml configured for TAVP: PHP 8.3 with the Phalcon
 * extension pre-installed, plus MySQL and Node services. This is the
 * recommended adapter for Windows developers (founder uses it).
 */
class LandoAdapter
{
    public function generate(string $projectName): string
    {
        return <<<YAML
name: {$projectName}
recipe: lamp
config:
  php: '8.3'
  webroot: public
  database: mysql
  build_as_root:
    - apt-get update
    - apt-get install -y software-properties-common
    - add-apt-repository ppa:ondrej/php -y
    - apt-get install -y php8.3-phalcon
  via: apache
services:
  node:
    type: node:20
  database:
    portforward: true
tooling:
  tavp:
    service: appserver
    cmd: php bin/tavp
YAML;
    }

    public function isAvailable(): bool
    {
        return getenv('LANDO') !== false || is_file('.lando.yml');
    }
}
