<?php

declare(strict_types=1);

use Tavp\Core\Infrastructure\Cache;
use Tavp\Core\Infrastructure\Queue;
use Tavp\Core\Infrastructure\Storage;
use Tavp\Core\Infrastructure\Schedule;
use PHPUnit\Framework\TestCase;

class InfrastructureTest extends TestCase
{
    public function testCacheSetGetForget(): void
    {
        $cache = new Cache('array');
        $cache->set('key', 'value');
        $this->assertSame('value', $cache->get('key'));
        $cache->forget('key');
        $this->assertNull($cache->get('key'));
    }

    public function testQueueRunsJobs(): void
    {
        $queue = new Queue();
        $ran = 0;
        $queue->push(function () use (&$ran) { $ran++; });
        $queue->push(function () use (&$ran) { $ran++; });
        $this->assertSame(2, $queue->work());
        $this->assertSame(2, $ran);
    }

    public function testStoragePutGet(): void
    {
        $dir = sys_get_temp_dir() . '/tavp_storage_' . uniqid();
        $storage = new Storage('local', $dir);
        $storage->put('a/b.txt', 'hello');
        $this->assertSame('hello', $storage->get('a/b.txt'));
        $this->assertTrue($storage->exists('a/b.txt'));
        array_map('unlink', glob($dir . '/*/*'));
        rmdir($dir . '/a');
        rmdir($dir);
    }

    public function testScheduleRunsTasks(): void
    {
        $schedule = new Schedule();
        $count = 0;
        $schedule->call(function () use (&$count) { $count++; }, 'daily');
        $this->assertSame(1, $schedule->run());
        $this->assertSame(1, $count);
    }
}
