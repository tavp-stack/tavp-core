<?php

declare(strict_types=1);

use Tavp\Core\Logging\Logger;
use Tavp\Core\Health\HealthCheck;
use Tavp\Core\Optimize;
use PHPUnit\Framework\TestCase;

class LoggingHealthTest extends TestCase
{
    public function testLoggerWritesJsonLine(): void
    {
        $file = sys_get_temp_dir() . '/tavp_log_test_' . uniqid() . '.log';
        $logger = new Logger('file', $file);
        $logger->info('Test message', ['user' => 1]);

        $lines = file($file);
        $entry = json_decode($lines[0], true);
        $this->assertSame('info', $entry['level']);
        $this->assertSame('Test message', $entry['message']);
        $this->assertArrayHasKey('request_id', $entry);
        unlink($file);
    }

    public function testHealthReportOkWhenAllHealthy(): void
    {
        $health = new HealthCheck();
        $health->addCheck('database', true);
        $health->addCheck('cache', true);
        $report = $health->report();
        $this->assertSame('ok', $report['status']);
        $this->assertTrue($health->isHealthy());
    }

    public function testHealthReportDegradedWhenOneFails(): void
    {
        $health = new HealthCheck();
        $health->addCheck('database', true);
        $health->addCheck('queue', false);
        $this->assertSame('degraded', $health->report()['status']);
    }

    public function testOptimizeRunsAllCaches(): void
    {
        $optimize = new Optimize();
        $result = $optimize->run();
        $this->assertTrue($result['config']);
        $this->assertTrue($result['routes']);
        $this->assertTrue($result['views']);
    }
}
