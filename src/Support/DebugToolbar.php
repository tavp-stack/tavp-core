<?php

declare(strict_types=1);

namespace Tavp\Core\Support;

/**
 * Debug toolbar — display request info, queries, timing in dev mode.
 */
class DebugToolbar
{
    private array $data = [];
    private float $startTime;
    private int $startMemory;
    private array $queries = [];

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
    }

    /**
     * Record a database query.
     */
    public function addQuery(string $sql, float $time, array $bindings = []): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'time' => $time,
            'bindings' => $bindings,
        ];
    }

    /**
     * Add custom data to toolbar.
     */
    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Generate toolbar HTML.
     */
    public function render(): string
    {
        $elapsed = round((microtime(true) - $this->startTime) * 1000, 2);
        $memory = round((memory_get_usage() - $this->startMemory) / 1024 / 1024, 2);
        $peakMemory = round(memory_get_peak_usage() / 1024 / 1024, 2);
        $queryCount = count($this->queries);
        $queryTime = array_sum(array_column($this->queries, 'time'));

        $html = '<div id="tavp-debug-toolbar" style="position:fixed;bottom:0;left:0;right:0;background:#1a1a2e;color:#e0e0e0;font-family:monospace;font-size:12px;padding:8px 16px;z-index:99999;border-top:2px solid #4f8ff7;display:flex;gap:16px;align-items:center;">';

        $html .= '<span style="font-weight:bold;color:#4f8ff7;">TAVP Debug</span>';
        $html .= "<span>{$elapsed}ms</span>";
        $html .= "<span>{$memory}MB</span>";
        $html .= "<span>Peak: {$peakMemory}MB</span>";
        $html .= "<span>Queries: {$queryCount} ({$queryTime}ms)</span>";

        foreach ($this->data as $key => $value) {
            $display = is_array($value) ? json_encode($value) : (string)$value;
            $html .= "<span>{$key}: {$display}</span>";
        }

        $html .= '<button onclick="document.getElementById(\'tavp-debug-toolbar\').style.display=\'none\'" style="background:none;border:none;color:#888;cursor:pointer;margin-left:auto;">×</button>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Check if debug toolbar should be displayed.
     */
    public function shouldDisplay(): bool
    {
        return env('APP_DEBUG', false) === true;
    }
}
