<?php

declare(strict_types=1);

/**
 * Default admin seeder — creates a default admin user for first login.
 */
class AdminSeeder
{
    public function run(): void
    {
        echo "  Seeding: AdminSeeder\n";
        echo "    Default admin user will be created via OTP on first login.\n";
        echo "    No database seeder needed for OTP-based auth.\n";
    }
}
