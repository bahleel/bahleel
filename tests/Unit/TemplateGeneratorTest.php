<?php

use App\Services\TemplateGenerator;

beforeEach(function () {
    $this->generator = new TemplateGenerator;
});

test('template generator can generate spider content', function () {
    $content = $this->generator->generateSpider('TestSpider', [
        'startUrls' => ['https://example.com'],
        'concurrency' => 2,
        'requestDelay' => 1,
        'downloaderMiddleware' => [],
        'itemProcessors' => [],
        'fields' => [],
    ]);

    expect($content)->toContain('class TestSpider')
        ->and($content)->toContain('extends BasicSpider')
        ->and($content)->toContain('https://example.com');
});

test('template generator includes start urls in spider', function () {
    $content = $this->generator->generateSpider('TestSpider', [
        'startUrls' => ['https://example.com', 'https://test.com'],
        'concurrency' => 2,
        'requestDelay' => 1,
        'downloaderMiddleware' => [],
        'itemProcessors' => [],
        'fields' => [],
    ]);

    expect($content)->toContain('https://example.com')
        ->and($content)->toContain('https://test.com');
});

test('template generator includes fields in spider', function () {
    $content = $this->generator->generateSpider('TestSpider', [
        'startUrls' => ['https://example.com'],
        'concurrency' => 2,
        'requestDelay' => 1,
        'downloaderMiddleware' => [],
        'itemProcessors' => [],
        'fields' => [
            'title' => 'h1',
            'price' => '.price',
        ],
    ]);

    expect($content)->toContain('title')
        ->and($content)->toContain('price')
        ->and($content)->toContain('h1')
        ->and($content)->toContain('.price');
});

test('template generator can generate middleware content', function () {
    $content = $this->generator->generateMiddleware('TestMiddleware', 'request');

    expect($content)->toContain('class TestMiddleware')
        ->and($content)->toContain('RequestMiddlewareInterface')
        ->and($content)->toContain('handleRequest');
});

test('template generator can generate processor content', function () {
    $content = $this->generator->generateProcessor('TestProcessor');

    expect($content)->toContain('class TestProcessor')
        ->and($content)->toContain('ItemProcessorInterface')
        ->and($content)->toContain('processItem');
});

test('template generator can generate exporter content', function () {
    $content = $this->generator->generateExporter('TestExporter');

    expect($content)->toContain('class TestExporter')
        ->and($content)->toContain('ExporterInterface')
        ->and($content)->toContain('export');
});
