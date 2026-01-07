<?php

use App\Models\SpiderRun;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('spider run can be created', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now(),
    ]);

    expect($run->spider_name)->toBe('TestSpider')
        ->and($run->status)->toBe('running')
        ->and($run->items_scraped)->toBe(0)
        ->and($run->requests_sent)->toBe(0)
        ->and($run->errors_count)->toBe(0);
});

test('spider run can calculate duration', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now()->subSeconds(10),
        'finished_at' => now(),
    ]);

    $run->calculateDuration();

    expect($run->duration_seconds)->toBeGreaterThanOrEqual(9)
        ->and($run->duration_seconds)->toBeLessThanOrEqual(11);
});

test('spider run has scraped items relationship', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now(),
    ]);

    $run->scrapedItems()->create([
        'spider_name' => 'TestSpider',
        'data' => ['title' => 'Test'],
        'hash' => 'hash_123',
    ]);

    expect($run->scrapedItems)->toHaveCount(1);
});

test('spider run has logs relationship', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now(),
    ]);

    $run->logs()->create([
        'level' => 'info',
        'message' => 'Test log message',
    ]);

    expect($run->logs)->toHaveCount(1);
});
