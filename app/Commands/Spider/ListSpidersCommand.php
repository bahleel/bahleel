<?php

namespace App\Commands\Spider;

use App\Services\SpiderManager;
use LaravelZero\Framework\Commands\Command;

class ListSpidersCommand extends Command
{
    protected $signature = 'spider:list';

    protected $description = 'List all available spiders';

    public function handle(SpiderManager $spiderManager): int
    {
        $spiders = $spiderManager->all();

        if (empty($spiders)) {
            $this->warn('No spiders found.');
            $this->comment('Create one with: php bahleel make:spider');

            return self::SUCCESS;
        }

        $this->info('Available Spiders:');
        $this->newLine();

        $rows = [];
        foreach ($spiders as $spider) {
            $info = $spiderManager->info($spider);
            $rows[] = [
                $spider,
                $info['has_middleware'] ? '✓' : '✗',
                $info['has_processors'] ? '✓' : '✗',
                round($info['size'] / 1024, 2).' KB',
            ];
        }

        $this->table(
            ['Name', 'Middleware', 'Processors', 'Size'],
            $rows
        );

        return self::SUCCESS;
    }
}
