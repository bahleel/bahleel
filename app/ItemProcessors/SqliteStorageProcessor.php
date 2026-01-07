<?php

namespace App\ItemProcessors;

use App\Models\ScrapedItem;
use App\Models\SpiderRun;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class SqliteStorageProcessor implements ItemProcessorInterface
{
    use Configurable;

    protected ?int $spiderRunId = null;

    protected string $spiderName = '';

    private array $defaultOptions = [
        'spider_run_id' => null,
        'spider_name' => '',
    ];

    /**
     * Process and store the item in SQLite.
     */
    public function processItem(ItemInterface $item): ItemInterface
    {
        $data = $item->all();

        // Get URL from item or request
        $url = $data['url'] ?? null;
        unset($data['url']); // Remove URL from data array

        // Get spider run ID
        $spiderRunId = $this->option('spider_run_id')
            ?? $this->getOrCreateSpiderRun($this->option('spider_name'));

        // Generate hash for duplicate detection
        $hash = ScrapedItem::generateHash($data, $url);

        try {
            // Check for duplicate
            $exists = ScrapedItem::where('hash', $hash)->exists();

            if ($exists && config('bahleel.duplicate_filter', true)) {
                // Skip duplicate
                return $item->drop('Duplicate item detected');
            }

            // Store in database
            ScrapedItem::create([
                'spider_name' => $this->option('spider_name'),
                'spider_run_id' => $spiderRunId,
                'data' => $data,
                'url' => $url,
                'hash' => $hash,
            ]);

            // Update run stats
            $this->updateRunStats($spiderRunId);

        } catch (\Exception $e) {
            // Log error but don't drop item
            logger()->error('Failed to store item', [
                'error' => $e->getMessage(),
                'item' => $data,
            ]);
        }

        return $item;
    }

    /**
     * Get or create spider run.
     */
    protected function getOrCreateSpiderRun(string $spiderName): int
    {
        if ($this->spiderRunId) {
            return $this->spiderRunId;
        }

        $run = SpiderRun::where('spider_name', $spiderName)
            ->where('status', 'running')
            ->latest()
            ->first();

        if (! $run) {
            $run = SpiderRun::create([
                'spider_name' => $spiderName,
                'status' => 'running',
                'started_at' => now(),
            ]);
        }

        $this->spiderRunId = $run->id;

        return $this->spiderRunId;
    }

    /**
     * Update spider run statistics.
     */
    protected function updateRunStats(int $spiderRunId): void
    {
        $run = SpiderRun::find($spiderRunId);

        if ($run) {
            $run->increment('items_scraped');
        }
    }
}
