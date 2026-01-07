<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class TemplateGenerator
{
    /**
     * Generate a spider file from template.
     */
    public function generateSpider(string $name, array $data): string
    {
        return View::make('templates.spider', array_merge([
            'className' => Str::studly($name),
        ], $data))->render();
    }

    /**
     * Generate a middleware file from template.
     */
    public function generateMiddleware(string $name, string $type, array $data = []): string
    {
        return View::make('templates.middleware', array_merge([
            'className' => Str::studly($name),
            'type' => $type,
        ], $data))->render();
    }

    /**
     * Generate an item processor file from template.
     */
    public function generateProcessor(string $name, array $data = []): string
    {
        return View::make('templates.processor', array_merge([
            'className' => Str::studly($name),
        ], $data))->render();
    }

    /**
     * Generate an exporter file from template.
     */
    public function generateExporter(string $name, array $data = []): string
    {
        return View::make('templates.exporter', array_merge([
            'className' => Str::studly($name),
        ], $data))->render();
    }

    /**
     * Save generated content to file.
     */
    public function save(string $path, string $content): bool
    {
        $directory = dirname($path);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return File::put($path, $content) !== false;
    }

    /**
     * Format PHP code.
     */
    protected function formatCode(string $code): string
    {
        // Remove extra blank lines
        $code = preg_replace("/\n{3,}/", "\n\n", $code);

        // Ensure file ends with newline
        if (! str_ends_with($code, "\n")) {
            $code .= "\n";
        }

        return $code;
    }
}
