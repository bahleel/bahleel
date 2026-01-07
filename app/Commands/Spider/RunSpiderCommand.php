<?php

namespace App\Commands\Spider;

use App\Services\SpiderManager;
use App\Services\SpiderRunner;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;

class RunSpiderCommand extends Command
{
    protected $signature = 'run:spider {name? : The spider to run}';

    protected $description = 'Run a spider to scrape data';

    public function handle(SpiderManager $spiderManager, SpiderRunner $runner): int
    {
        $spiders = $spiderManager->all();

        if (empty($spiders)) {
            $this->error('No spiders found!');
            $this->comment('Create one with: php bahleel make:spider');

            return self::FAILURE;
        }

        $name = $this->argument('name');

        if (! $name) {
            $name = select(
                label: 'Which spider do you want to run?',
                options: $spiders,
            );
        }

        if (! $spiderManager->exists($name)) {
            $this->error("Spider '{$name}' not found!");

            return self::FAILURE;
        }

        $this->info("Running spider: {$name}");
        $this->newLine();

        try {
            $spiderRun = spin(
                callback: fn () => $runner->run($name),
                message: 'Scraping data...',
            );

            $this->newLine();
            $this->info('✓ Spider completed successfully!');
            $this->newLine();

            // Show stats
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Items Scraped', $spiderRun->items_scraped],
                    ['Duration', $spiderRun->duration_seconds.'s'],
                    ['Status', $spiderRun->status],
                ]
            );

            $this->newLine();
            $this->comment('View data with: php bahleel data:show '.$name);
            $this->comment('Export data with: php bahleel export:csv '.$name);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Spider failed!');
            $this->error($e->getMessage());

            if ($this->option('verbose')) {
                $this->newLine();
                $this->line($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
