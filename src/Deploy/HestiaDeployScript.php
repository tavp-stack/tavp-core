<?php

declare(strict_types=1);

namespace Tavp\Deploy;

/**
 * HestiaCP deployment integration — shell script generation.
 */
class HestiaDeployScript
{
    /**
     * Generate the HestiaCP setup script for a TAVP project.
     */
    public static function generate(array $config): string
    {
        $domain = $config['domain'] ?? 'example.com';
        $user = $config['user'] ?? 'admin';
        $phpVersion = $config['php_version'] ?? '8.3';
        $dbName = $config['db_name'] ?? 'tavp';
        $dbUser = $config['db_user'] ?? 'tavp_user';

        return <<<BASH
#!/bin/bash
set -e

# TAVP Stack — HestiaCP Deployment Script
# Generated: date('Y-m-d H:i:s')

DOMAIN="{$domain}"
USER="{$user}"
PHP_VERSION="{$phpVersion}"
DB_NAME="{$dbName}"
DB_USER="{$dbUser}"

echo "=== TAVP Stack Deployment for HestiaCP ==="
echo ""

# 1. Create web domain
echo "[1/6] Creating web domain..."
v-add-web-domain {$user} {$domain}

# 2. Create database
echo "[2/6] Creating database..."
DB_PASS=\$(openssl rand -hex 16)
v-add-database-host {$user} localhost
v-add-database {$user} {$dbUser} {$dbName} "\${DB_PASS}"
echo "Database credentials saved: {$dbUser} / \${DB_PASS}"

# 3. Configure PHP
echo "[3/6] Configuring PHP..."
v-change-web-domain-backend-port {$user} {$domain} 9000

# 4. Setup composer
echo "[4/6] Installing dependencies..."
v-run-script web-domain {$user} {$domain} "cd /home/{$user}/web/{$domain}/public_html && composer install --no-dev --optimize-autoloader"

# 5. Run migrations
echo "[5/6] Running migrations..."
v-run-script web-domain {$user} {$domain} "cd /home/{$user}/web/{$domain}/public_html && php bin/tavp migrate --force"

# 6. Set permissions
echo "[6/6] Setting permissions..."
chown -R {$user}:www-data /home/{$user}/web/{$domain}/public_html
chmod -R 755 /home/{$user}/web/{$domain}/public_html/storage
chmod -R 755 /home/{$user}/web/{$domain}/public_html/bootstrap/cache

echo ""
echo "=== Deployment Complete ==="
echo "Domain: https://{$domain}"
echo "Database: {$dbName}"
echo "Database User: {$dbUser}"
echo "Database Password: \${DB_PASS}"
echo ""
echo "Save these credentials securely!"
BASH;
    }
}
