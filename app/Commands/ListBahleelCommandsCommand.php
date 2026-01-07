<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ListBahleelCommandsCommand extends Command
{
    protected $signature = 'list';

    protected $description = 'List all Bahleel commands';

    public function handle(): int
    {
        $this->info('🕷️  Bahleel - PHP Web Scraping Framework');
        $this->newLine();

        $this->line('<fg=yellow>Spider Management</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['make:spider', 'Create a new spider'],
                ['run:spider {name}', 'Run a spider to scrape data'],
                ['spider:list', 'List all available spiders'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Data Management</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['data:show {spider}', 'Show scraped data from database'],
                ['data:clear {spider}', 'Clear scraped data for a spider'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Export</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['export:csv {spider}', 'Export data to CSV format'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Database</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['migrate', 'Run database migrations'],
                ['migrate:fresh', 'Drop all tables and re-run migrations'],
                ['migrate:rollback', 'Rollback the last migration'],
                ['db:wipe', 'Drop all tables (fresh start)'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Generators</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['make:spider', 'Create a new spider'],
                ['make:command', 'Create a new command'],
                ['make:model', 'Create a new Eloquent model'],
                ['make:migration', 'Create a new migration file'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Testing</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['test', 'Run the test suite'],
                ['test --unit', 'Run only Unit tests'],
                ['test --feature', 'Run only Feature tests'],
                ['test --filter={name}', 'Filter tests by name'],
                ['make:test', 'Create a new test class'],
            ]
        );

        $this->newLine();
        $this->line('<fg=yellow>Build</>');
        $this->table(
            ['Command', 'Description'],
            [
                ['app:build', 'Build a standalone executable'],
            ]
        );

        $this->newLine();
        $this->comment('📖 Documentation: https://github.com/bahleel/bahleel');
        $this->comment('🐛 Issues: https://github.com/bahleel/bahleel/issues');

        return self::SUCCESS;
    }
}
