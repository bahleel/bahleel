<?php

namespace App\Commands\Data;

use App\Models\ScrapedItem;
use App\Models\SpiderRun;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class ClearDataCommand extends Command
{
    protected $signature = 'data:clear {spider? : Spider name} {--all : Clear all data}';

    protected $description = 'Clear scraped data from database';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->clearAllData();
        }

        $spiderName = $this->argument('spider');

        if (! $spiderName) {
            $spiders = ScrapedItem::select('spider_name')
                ->distinct()
                ->pluck('spider_name')
                ->toArray();

            if (empty($spiders)) {
                $this->warn('No data found in database.');

                return self::SUCCESS;
            }

            $spiders[] = '--- Clear All Data ---';

            $spiderName = select(
                label: 'Select spider to clear data',
                options: $spiders,
            );

            if ($spiderName === '--- Clear All Data ---') {
                return $this->clearAllData();
            }
        }

        return $this->clearSpiderData($spiderName);
    }

    /**
     * Clear data for a specific spider.
     */
    protected function clearSpiderData(string $spiderName): int
    {
        $itemsCount = ScrapedItem::where('spider_name', $spiderName)->count();

        if ($itemsCount === 0) {
            $this->warn("No data found for spider: {$spiderName}");

            return self::SUCCESS;
        }

        $confirmed = confirm(
            label: "Delete {$itemsCount} items for spider '{$spiderName}'?",
            default: false,
        );

        if (! $confirmed) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        // Delete scraped items
        ScrapedItem::where('spider_name', $spiderName)->delete();

        // Delete spider runs and logs (cascade will handle logs)
        SpiderRun::where('spider_name', $spiderName)->delete();

        $this->info("✓ Cleared {$itemsCount} items for spider: {$spiderName}");

        return self::SUCCESS;
    }

    /**
     * Clear all data from database.
     */
    protected function clearAllData(): int
    {
        $itemsCount = ScrapedItem::count();
        $runsCount = SpiderRun::count();

        if ($itemsCount === 0) {
            $this->warn('No data found in database.');

            return self::SUCCESS;
        }

        $confirmed = confirm(
            label: "Delete ALL data? ({$itemsCount} items, {$runsCount} runs)",
            default: false,
        );

        if (! $confirmed) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        // Delete all data
        ScrapedItem::truncate();
        SpiderRun::truncate();

        $this->info('✓ Cleared all data from database');
        $this->comment("- {$itemsCount} items deleted");
        $this->comment("- {$runsCount} spider runs deleted");

        return self::SUCCESS;
    }
}
