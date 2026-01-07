<?php

use App\Services\SpiderManager;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->spiderManager = new SpiderManager;

    // Clean up test spiders
    $testSpiders = ['TestSpider', 'AnotherTestSpider'];
    foreach ($testSpiders as $spider) {
        $path = $this->spiderManager->getSpiderPath($spider);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

afterEach(function () {
    // Clean up test spiders
    $testSpiders = ['TestSpider', 'AnotherTestSpider'];
    foreach ($testSpiders as $spider) {
        $path = $this->spiderManager->getSpiderPath($spider);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

test('spider manager can check if spider exists', function () {
    expect($this->spiderManager->exists('NonExistentSpider'))->toBeFalse();
});

test('spider manager returns correct spider path', function () {
    $path = $this->spiderManager->getSpiderPath('TestSpider');

    expect($path)->toContain('spiders')
        ->and($path)->toContain('TestSpider.php');
});

test('spider manager returns correct spider class name', function () {
    $class = $this->spiderManager->getSpiderClass('TestSpider');

    expect($class)->toBe('Spiders\\TestSpider');
});

test('spider manager can list all spiders', function () {
    $spiders = $this->spiderManager->all();

    expect($spiders)->toBeArray();
});

test('spider manager returns spider info when spider exists', function () {
    // Create a test spider file
    $path = $this->spiderManager->getSpiderPath('TestSpider');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, '<?php namespace Spiders; class TestSpider {}');

    $info = $this->spiderManager->info('TestSpider');

    expect($info)->toBeArray()
        ->and($info)->toHaveKey('name')
        ->and($info)->toHaveKey('path')
        ->and($info)->toHaveKey('class');
});

test('spider manager returns null for non-existent spider info', function () {
    $info = $this->spiderManager->info('NonExistentSpider');

    expect($info)->toBeNull();
});
