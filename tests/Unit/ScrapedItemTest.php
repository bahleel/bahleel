<?php

use App\Models\ScrapedItem;
use App\Models\SpiderRun;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('scraped item can be created', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now(),
    ]);

    $item = ScrapedItem::create([
        'spider_name' => 'TestSpider',
        'spider_run_id' => $run->id,
        'data' => ['title' => 'Test Title'],
        'url' => 'https://example.com',
        'hash' => 'test_hash_123',
    ]);

    expect($item->spider_name)->toBe('TestSpider')
        ->and($item->data)->toBeArray()
        ->and($item->data['title'])->toBe('Test Title');
});

test('scraped item belongs to spider run', function () {
    $run = SpiderRun::create([
        'spider_name' => 'TestSpider',
        'status' => 'running',
        'started_at' => now(),
    ]);

    $item = ScrapedItem::create([
        'spider_name' => 'TestSpider',
        'spider_run_id' => $run->id,
        'data' => ['title' => 'Test'],
        'hash' => 'hash_123',
    ]);

    expect($item->spiderRun->id)->toBe($run->id);
});

test('scraped item can generate hash', function () {
    $data = ['title' => 'Test', 'price' => '100'];
    $url = 'https://example.com';

    $hash = ScrapedItem::generateHash($data, $url);

    expect($hash)->toBeString()
        ->and(strlen($hash))->toBe(64); // SHA-256 produces 64 character hash
});

test('duplicate hashes generate same hash', function () {
    $data = ['title' => 'Test', 'price' => '100'];
    $url = 'https://example.com';

    $hash1 = ScrapedItem::generateHash($data, $url);
    $hash2 = ScrapedItem::generateHash($data, $url);

    expect($hash1)->toBe($hash2);
});
