<?php

namespace App\Commands\Make;

use App\Services\SpiderManager;
use App\Services\TemplateGenerator;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class MakeSpiderCommand extends Command
{
    protected $signature = 'make:spider {name? : The name of the spider}';

    protected $description = 'Create a new spider for web scraping';

    public function handle(SpiderManager $spiderManager, TemplateGenerator $generator): int
    {
        $name = $this->argument('name') ?? text(
            label: 'What is the spider name?',
            placeholder: 'E.g., MySpider',
            required: true,
        );

        $name = Str::studly($name);

        // Check if spider already exists
        if ($spiderManager->exists($name)) {
            $this->error("Spider '{$name}' already exists!");

            return self::FAILURE;
        }

        $this->info("Creating spider: {$name}");
        $this->newLine();

        // Get start URLs
        $startUrlsInput = text(
            label: 'Start URL(s)',
            placeholder: 'https://example.com (separate multiple URLs with comma)',
            required: true,
        );

        $startUrls = array_map('trim', explode(',', $startUrlsInput));

        // Get concurrency
        $concurrency = (int) text(
            label: 'Concurrency (concurrent requests)',
            default: config('bahleel.concurrency', 2),
            validate: fn ($value) => is_numeric($value) && $value > 0
                ? null
                : 'Must be a positive number',
        );

        // Get request delay
        $requestDelay = (int) text(
            label: 'Request delay (seconds)',
            default: config('bahleel.request_delay', 1),
            validate: fn ($value) => is_numeric($value) && $value >= 0
                ? null
                : 'Must be 0 or greater',
        );

        $this->newLine();

        // Ask about middleware
        $createMiddleware = confirm(
            label: 'Do you want to create custom middleware?',
            default: false,
        );

        $downloaderMiddleware = [];
        if ($createMiddleware) {
            $middlewareTypes = multiselect(
                label: 'Select middleware types',
                options: [
                    'proxy' => 'Proxy Middleware',
                    'javascript' => 'JavaScript Execution',
                    'user-agent' => 'User Agent',
                    'cookies' => 'Cookie Management',
                    'deduplication' => 'Request Deduplication',
                ],
                default: ['user-agent', 'deduplication'],
            );

            $downloaderMiddleware = $this->buildMiddlewareArray($middlewareTypes);
        }

        $this->newLine();

        // Ask about item processor
        $createProcessor = confirm(
            label: 'Do you want to extract data automatically?',
            default: true,
        );

        $fields = [];
        $itemProcessors = [
            '\\App\\ItemProcessors\\SqliteStorageProcessor::class',
        ];

        if ($createProcessor) {
            $this->info('Define fields to extract (CSS selectors)');
            $addMore = true;

            while ($addMore) {
                $fieldName = text(
                    label: 'Field name',
                    placeholder: 'e.g., title, price, description',
                    required: false,
                );

                if (! $fieldName) {
                    break;
                }

                $selector = text(
                    label: "CSS selector for '{$fieldName}'",
                    placeholder: 'e.g., h1.title, .price, p.description',
                    required: true,
                );

                $fields[$fieldName] = $selector;

                $addMore = confirm('Add another field?', default: false);
            }
        }

        // Generate spider
        $spiderContent = $generator->generateSpider($name, [
            'startUrls' => $startUrls,
            'concurrency' => $concurrency,
            'requestDelay' => $requestDelay,
            'downloaderMiddleware' => $downloaderMiddleware,
            'itemProcessors' => $itemProcessors,
            'fields' => $fields,
        ]);

        $spiderPath = $spiderManager->getSpiderPath($name);
        $generator->save($spiderPath, $spiderContent);

        $this->newLine();
        $this->info('✓ Spider created successfully!');
        $this->newLine();

        // Show summary
        table(
            ['Property', 'Value'],
            [
                ['Name', $name],
                ['Path', $spiderPath],
                ['Start URLs', implode(', ', $startUrls)],
                ['Concurrency', $concurrency],
                ['Request Delay', $requestDelay.'s'],
                ['Middleware', count($downloaderMiddleware).' configured'],
                ['Fields', count($fields).' defined'],
            ]
        );

        $this->newLine();
        $this->comment('Run your spider with:');
        $this->line("  php bahleel run:spider {$name}");

        return self::SUCCESS;
    }

    /**
     * Build middleware array configuration.
     */
    protected function buildMiddlewareArray(array $types): array
    {
        $middleware = [];

        foreach ($types as $type) {
            $middleware[] = match ($type) {
                'proxy' => '\\RoachPHP\\Downloader\\Middleware\\ProxyMiddleware::class',
                'javascript' => '\\RoachPHP\\Downloader\\Middleware\\ExecuteJavascriptMiddleware::class',
                'user-agent' => '[\\RoachPHP\\Downloader\\Middleware\\UserAgentMiddleware::class, [\'userAgent\' => \'Mozilla/5.0 (compatible; Bahleel/1.0)\']]',
                'cookies' => '\\RoachPHP\\Downloader\\Middleware\\CookieMiddleware::class',
                'deduplication' => '\\RoachPHP\\Downloader\\Middleware\\RequestDeduplicationMiddleware::class',
            };
        }

        return $middleware;
    }
}
