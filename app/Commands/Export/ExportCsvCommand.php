<?php

namespace App\Commands\Export;

use App\Exporters\CsvExporter;
use App\Models\ScrapedItem;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\select;

class ExportCsvCommand extends Command
{
    protected $signature = 'export:csv {spider? : Spider name} {--output= : Output file path}';

    protected $description = 'Export scraped data to CSV format';

    public function handle(CsvExporter $exporter): int
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
                label: 'Select spider to export',
                options: $spiders,
            );
        }

        $items = ScrapedItem::where('spider_name', $spiderName)->get();

        if ($items->isEmpty()) {
            $this->warn("No data found for spider: {$spiderName}");

            return self::SUCCESS;
        }

        // Prepare output path
        $outputPath = $this->option('output') ?? storage_path('exports/'.$spiderName.'_'.date('Y-m-d_His').'.csv');

        // Ensure directory exists
        $dir = dirname($outputPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Format data for export
        $data = $items->map(function ($item) {
            $row = $item->data;
            if ($item->url) {
                $row['url'] = $item->url;
            }
            $row['scraped_at'] = $item->created_at->toDateTimeString();

            return $row;
        });

        // Export
        if ($exporter->export($data, $outputPath)) {
            $this->info("✓ Exported {$items->count()} items to:");
            $this->line("  {$outputPath}");

            return self::SUCCESS;
        }

        $this->error('Failed to export data.');

        return self::FAILURE;
    }
}
