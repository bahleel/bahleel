<?php

use App\Exporters\CsvExporter;
use App\Exporters\JsonExporter;
use Illuminate\Support\Facades\File;

afterEach(function () {
    // Clean up test files
    $testFiles = [
        storage_path('test_export.csv'),
        storage_path('test_export.json'),
    ];

    foreach ($testFiles as $file) {
        if (File::exists($file)) {
            File::delete($file);
        }
    }
});

test('csv exporter can export data', function () {
    $exporter = new CsvExporter;
    $data = collect([
        ['name' => 'Item 1', 'price' => '100'],
        ['name' => 'Item 2', 'price' => '200'],
    ]);

    $path = storage_path('test_export.csv');
    $result = $exporter->export($data, $path);

    expect($result)->toBeTrue()
        ->and(File::exists($path))->toBeTrue();

    $content = File::get($path);
    expect($content)->toContain('name')
        ->and($content)->toContain('Item 1')
        ->and($content)->toContain('Item 2');
});

test('csv exporter returns correct extension', function () {
    $exporter = new CsvExporter;

    expect($exporter->getExtension())->toBe('csv');
});

test('csv exporter returns false for empty data', function () {
    $exporter = new CsvExporter;
    $data = collect([]);

    $path = storage_path('test_export.csv');
    $result = $exporter->export($data, $path);

    expect($result)->toBeFalse();
});

test('json exporter can export data', function () {
    $exporter = new JsonExporter;
    $data = collect([
        ['name' => 'Item 1', 'price' => '100'],
        ['name' => 'Item 2', 'price' => '200'],
    ]);

    $path = storage_path('test_export.json');
    $result = $exporter->export($data, $path);

    expect($result)->toBeTrue()
        ->and(File::exists($path))->toBeTrue();

    $content = File::get($path);
    $decoded = json_decode($content, true);

    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveCount(2)
        ->and($decoded[0]['name'])->toBe('Item 1');
});

test('json exporter returns correct extension', function () {
    $exporter = new JsonExporter;

    expect($exporter->getExtension())->toBe('json');
});
