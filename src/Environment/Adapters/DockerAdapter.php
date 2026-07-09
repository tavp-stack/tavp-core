<?php

declare(strict_types=1);

namespace Tavp\Environment\Adapters;

/**
 * Docker environment adapter — generates docker-compose.yml and Dockerfile.
 */
class DockerAdapter
{
    public function getName(): string
    {
        return 'docker';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['docker-compose.yml'] = $this->dockerCompose($config);
        $files['Dockerfile'] = $this->dockerfile($config);
        $files['docker/php.ini'] = $this->phpIni();
        $files['docker/nginx.conf'] = $this->nginxConf($config);

        return $files;
    }

    private function dockerCompose(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';
        $dbDriver = $config['db_driver'] ?? 'mysql';
        $dbName = $config['db_name'] ?? 'tavp';

        $dbService = match ($dbDriver) {
            'mysql' => <<<YAML
  db:
    image: mariadb:11
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: {$dbName}
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

YAML,
            'postgresql' => <<<YAML
  db:
    image: postgres:16
    environment:
      POSTGRES_DB: {$dbName}
      POSTGRES_USER: tavp
      POSTGRES_PASSWORD: secret
    ports:
      - "5432:5432"
    volumes:
      - db_data:/var/lib/postgresql/data

YAML,
            'sqlite' => '',
            default => '',
        };

        return <<<YAML
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    volumes:
      - .:/app
    depends_on:
      - db
    environment:
      DB_CONNECTION: {$dbDriver}
      DB_HOST: db
      DB_DATABASE: {$dbName}
{$dbService}
  cache:
    image: redis:7-alpine
    ports:
      - "6379:6379"

volumes:
  db_data:

YAML;
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

WORKDIR /app

COPY composer.json composer.lock* ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
DOCKER;
    }

    private function phpIni(): string
    {
        return <<<INI
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 30
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
date.timezone = UTC
INI;
    }

    private function nginxConf(array $config): string
    {
        $serverName = $config['domain'] ?? 'localhost';

        return <<<NGINX
server {
    listen 80;
    server_name {$serverName};
    root /app/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX;
    }
}
