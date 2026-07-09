<?php

declare(strict_types=1);

namespace Tavp\Core\Debug;

/**
 * Lightweight debug toolbar shown only in local environment.
 *
 * Collects request info, executed queries, timeline and memory so
 * developers can inspect a page without external tooling.
 */
class DebugToolbar
{
    private array $queries = [];
    private float $startTime;
    private array $timeline = [];

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Record a database query for the toolbar.
     */
    public function addQuery(string $sql, float $durationMs): void
    {
        $this->queries[] = ['sql' => $sql, 'duration_ms' => $durationMs];
    }

    /**
     * Record a timeline milestone.
     */
    public function mark(string $label): void
    {
        $this->timeline[] = ['label' => $label, 'at_ms' => (microtime(true) - $this->startTime) * 1000];
    }

    /**
     * Render the toolbar as a simple HTML panel.
     */
    public function render(): string
    {
        $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $time = round((microtime(true) - $this->startTime) * 1000, 2);

        return sprintf(
            '<div id="tavp-debug" style="position:fixed;bottom:0;left:0;background:#111;color:#0f0;font:12px monospace;padding:8px;z-index:9999">'
            . 'TAVP Debug | %s ms | %s MB | %d queries</div>',
            $time,
            $memory,
            count($this->queries)
        );
    }
}
