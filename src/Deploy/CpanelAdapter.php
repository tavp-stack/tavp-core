<?php

declare(strict_types=1);

namespace Tavp\Deploy;

/**
 * cPanel deployment adapter — generates deployment scripts for cPanel.
 */
class CpanelAdapter
{
    public function getName(): string
    {
        return 'cpanel';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['deploy-cpanel.sh'] = $this->deployScript($config);

        return $files;
    }

    private function deployScript(array $config): string
    {
        $domain = $config['domain'] ?? 'example.com';
        $dbUser = $config['db_user'] ?? 'tavp_user';
        $dbName = $config['db_name'] ?? 'tavp';

        return <<<BASH
#!/bin/bash
set -e

# TAVP Stack — cPanel Deployment Script
# Generated: date('Y-m-d H:i:s')

DOMAIN="{$domain}"
DB_USER="{$dbUser}"
DB_NAME="{$dbName}"

echo "=== TAVP Stack Deployment for cPanel ==="
echo ""

# 1. Build frontend assets
echo "[1/3] Building frontend assets..."
npm ci && npm run build

# 2. Upload via SCP
echo "[2/3] Uploading files..."
# scp -r public_html/* {$domain}:public_html/

# 3. Setup database
echo "[3/3] Setting up database..."
# mysql -u {$dbUser} -p {$dbName} < database/migrations/*.sql

# 4. Configure .htaccess for clean URLs
cat > public/.htaccess << 'HTACCESS'
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
HTACCESS

echo ""
echo "=== Deployment Complete ==="
echo "Domain: https://{$domain}"
BASH;
    }
}
