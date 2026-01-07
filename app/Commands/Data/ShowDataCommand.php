<?php

namespace App\Commands\Data;

use App\Models\ScrapedItem;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\select;

class ShowDataCommand extends Command
{
    protected $signature = 'data:show {spider? : Spider name} {--limit=10 : Number of items to show}';

    protected $description = 'Show scraped data from database';

    public function handle(): int
    {
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

            $spiderName = select(
                label: 'Select spider to view data',
                options: $spiders,
            );
        }

        $limit = (int) $this->option('limit');

        $items = ScrapedItem::where('spider_name', $spiderName)
            ->latest()
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            $this->warn("No data found for spider: {$spiderName}");

            return self::SUCCESS;
        }

        $this->info("Showing latest {$items->count()} items from: {$spiderName}");
        $this->newLine();

        foreach ($items as $index => $item) {
            $this->info('Item #'.($index + 1));
            $this->table(
                ['Field', 'Value'],
                collect($item->data)->map(fn ($value, $key) => [$key, $value])->toArray()
            );
            if ($item->url) {
                $this->line("URL: {$item->url}");
            }
            $this->newLine();
        }

        // Show total count
        $total = ScrapedItem::where('spider_name', $spiderName)->count();
        $this->comment("Total items in database: {$total}");

        return self::SUCCESS;
    }
}
