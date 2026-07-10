<?php

declare(strict_types=1);

namespace Tavp\Core\Environment\Adapters;

/**
 * DevContainer environment adapter — generates devcontainer.json.
 */
class DevContainerAdapter
{
    public function getName(): string
    {
        return 'devcontainer';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['.devcontainer/devcontainer.json'] = $this->devcontainerJson($config);
        $files['.devcontainer/Dockerfile'] = $this->dockerfile($config);

        return $files;
    }

    private function devcontainerJson(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';

        return <<<JSON
{
    "name": "TAVP Development",
    "build": {
        "dockerfile": "Dockerfile",
        "context": "."
    },
    "features": {
        "ghcr.io/devcontainers/features/git:1": {}
    },
    "forwardPorts": [8000],
    "postCreateCommand": "composer install && npm install",
    "customizations": {
        "vscode": {
            "extensions": [
                "bmewburn.vscode-intelephense-client",
                "bradlc.vscode-tailwindcss",
                "alpine-tailwindcss.vscode-tailwindcss"
            ]
        }
    }
}
JSON;
    }

    private function dockerfile(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';

        return <<<DOCKER
FROM php:{$phpVersion}-cli

RUN apt-get update && apt-get install -y \\
    git unzip libpng-dev libjpeg-dev libfreetype6-dev \\
    && docker-php-ext-configure gd --with-freetype --with-jpeg \\
    && docker-php-ext-install pdo pdo_mysql gd \\
    && pecl install phalcon && docker-php-ext-enable phalcon \\
    && apt-get clean

WORKDIR /workspace

COPY composer.json composer.lock* ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction

COPY package.json package-lock.json* ./
RUN npm install
DOCKER;
    }
}
