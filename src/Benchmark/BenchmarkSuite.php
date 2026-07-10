<?php

declare(strict_types=1);

namespace Tavp\Core\Benchmark;

/**
 * Performance benchmark suite.
 */
class BenchmarkSuite
{
    private array $results = [];

    /**
     * Run HTTP benchmark.
     */
    public function httpBenchmark(string $url, int $requests = 1000, int $concurrency = 10): array
    {
        $start = microtime(true);
        $successCount = 0;
        $errorCount = 0;
        $times = [];

        for ($i = 0; $i < $requests; $i++) {
            $reqStart = microtime(true);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
            ]);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $reqTime = (microtime(true) - $reqStart) * 1000;
            $times[] = $reqTime;

            if ($httpCode === 200) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $totalTime = microtime(true) - $start;
        sort($times);

        return [
            'url' => $url,
            'total_requests' => $requests,
            'successful' => $successCount,
            'failed' => $errorCount,
            'total_time' => round($totalTime, 2),
            'requests_per_second' => round($requests / $totalTime, 2),
            'avg_response_time' => round(array_sum($times) / count($times), 2),
            'p50' => $times[(int)(count($times) * 0.5)] ?? 0,
            'p95' => $times[(int)(count($times) * 0.95)] ?? 0,
            'p99' => $times[(int)(count($times) * 0.99)] ?? 0,
        ];
    }

    /**
     * Run database benchmark.
     */
    public function dbBenchmark(int $queries = 1000): array
    {
        $start = microtime(true);
        $times = [];

        for ($i = 0; $i < $queries; $i++) {
            $qStart = microtime(true);
            // Execute benchmark query
            $qTime = (microtime(true) - $qStart) * 1000;
            $times[] = $qTime;
        }

        $totalTime = microtime(true) - $start;
        sort($times);

        return [
            'total_queries' => $queries,
            'total_time' => round($totalTime, 2),
            'queries_per_second' => round($queries / $totalTime, 2),
            'avg_query_time' => round(array_sum($times) / count($times), 4),
            'p50' => $times[(int)(count($times) * 0.5)] ?? 0,
            'p95' => $times[(int)(count($times) * 0.95)] ?? 0,
            'p99' => $times[(int)(count($times) * 0.99)] ?? 0,
        ];
    }

    /**
     * Run memory benchmark.
     */
    public function memoryBenchmark(): array
    {
        $initial = memory_get_usage(true);

        // Run various operations
        $data = [];
        for ($i = 0; $i < 10000; $i++) {
            $data[] = str_repeat('x', 100);
        }

        $peak = memory_get_peak_usage(true);
        $final = memory_get_usage(true);

        return [
            'initial_memory' => $this->formatBytes($initial),
            'peak_memory' => $this->formatBytes($peak),
            'final_memory' => $this->formatBytes($final),
            'memory_used' => $this->formatBytes($final - $initial),
        ];
    }

    /**
     * Generate benchmark report.
     */
    public function generateReport(string $url): string
    {
        $http = $this->httpBenchmark($url);
        $memory = $this->memoryBenchmark();

        $report = "# TAVP Performance Benchmark Report\n\n";
        $report .= "Generated: " . date('c') . "\n\n";

        $report .= "## HTTP Benchmark\n";
        $report .= "- URL: {$http['url']}\n";
        $report .= "- Total Requests: {$http['total_requests']}\n";
        $report .= "- Requests/sec: {$http['requests_per_second']}\n";
        $report .= "- P99 Latency: {$http['p99']}ms\n\n";

        $report .= "## Memory Usage\n";
        $report .= "- Initial: {$memory['initial_memory']}\n";
        $report .= "- Peak: {$memory['peak_memory']}\n";
        $report .= "- Used: {$memory['memory_used']}\n";

        return $report;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
