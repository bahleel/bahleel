<?php

use App\Services\SpiderManager;
use Illuminate\Support\Facades\File;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->spiderManager = new SpiderManager;
});

afterEach(function () {
    // Clean up test spiders
    $path = $this->spiderManager->getSpiderPath('QuickTestSpider');
    if (File::exists($path)) {
        File::delete($path);
    }
});

test('list spiders command shows existing spiders', function () {
    // Create a test spider
    $path = $this->spiderManager->getSpiderPath('QuickTestSpider');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, '<?php namespace Spiders; use RoachPHP\Spider\BasicSpider; class QuickTestSpider extends BasicSpider {}');

    $this->artisan('spider:list')
        ->expectsOutputToContain('QuickTestSpider')
        ->assertSuccessful();
});

test('list command shows available commands', function () {
    $this->artisan('list')
        ->expectsOutputToContain('Spider Management')
        ->assertSuccessful();
});
