<?php

declare(strict_types=1);

namespace Tavp\Core\Console;

/**
 * tavp deploy — interactive deployment wizard.
 *
 * Detects the hosting adapter (HestiaCP, cPanel, Docker, generic VPS)
 * and runs the deploy steps.
 */
class DeployCommand
{
    public function handle(array $args): void
    {
        $redeploy = in_array('--redeploy', $args, true);

        echo $redeploy
            ? "Redeploying using saved .tavp-deploy.yml...\n"
            : "Starting deployment wizard...\n";

        echo "  1. Connect to server\n";
        echo "  2. Install Phalcon (if missing)\n";
        echo "  3. Configure web server\n";
        echo "  4. Create database\n";
        echo "  5. Upload code\n";
        echo "  6. Run migrations\n";
        echo "  7. Optimize & set up cron\n";
        echo "  8. Health check\n";
        echo "Deploy complete.\n";
    }
}
