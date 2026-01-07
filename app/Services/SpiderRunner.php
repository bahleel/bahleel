<?php

namespace App\Services;

use App\Models\SpiderLog;
use App\Models\SpiderRun;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

class SpiderRunner
{
    protected SpiderManager $spiderManager;

    protected SpiderRun $currentRun;

    public function __construct(SpiderManager $spiderManager)
    {
        $this->spiderManager = $spiderManager;
    }

    /**
     * Run a spider.
     */
    public function run(string $name, array $options = []): SpiderRun
    {
        $spiderClass = $this->spiderManager->load($name);

        if (! $spiderClass) {
            throw new \Exception("Spider '{$name}' not found");
        }

        // Create spider run record
        $this->currentRun = SpiderRun::create([
            'spider_name' => $name,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            SpiderLog::info($this->currentRun->id, "Starting spider: {$name}");

            // Prepare overrides
            $overrides = $this->buildOverrides($options);

            // Run the spider
            $items = Roach::collectSpider($spiderClass, $overrides);

            // Update run stats
            $this->currentRun->update([
                'status' => 'completed',
                'finished_at' => now(),
                'items_scraped' => count($items),
            ]);

            $this->currentRun->calculateDuration();

            SpiderLog::info($this->currentRun->id, 'Spider completed successfully', [
                'items_count' => count($items),
            ]);

            return $this->currentRun;
        } catch (\Exception $e) {
            $this->currentRun->update([
                'status' => 'failed',
                'finished_at' => now(),
                'errors_count' => 1,
            ]);

            $this->currentRun->calculateDuration();

            SpiderLog::error($this->currentRun->id, 'Spider failed: '.$e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Build Roach overrides from options.
     */
    protected function buildOverrides(array $options): ?Overrides
    {
        if (empty($options)) {
            return null;
        }

        return new Overrides(
            startUrls: $options['start_urls'] ?? null,
            concurrency: $options['concurrency'] ?? null,
            requestDelay: $options['request_delay'] ?? null,
        );
    }

    /**
     * Get current run.
     */
    public function getCurrentRun(): ?SpiderRun
    {
        return $this->currentRun ?? null;
    }
}
