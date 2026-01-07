<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SpiderManager
{
    protected string $spidersPath;

    protected string $namespace = 'Spiders';

    public function __construct()
    {
        $this->spidersPath = config('bahleel.spiders_path');
    }

    /**
     * Check if a spider exists.
     */
    public function exists(string $name): bool
    {
        return File::exists($this->getSpiderPath($name));
    }

    /**
     * Get the full path to a spider file.
     */
    public function getSpiderPath(string $name): string
    {
        $name = Str::studly($name);

        return $this->spidersPath.'/'.$name.'.php';
    }

    /**
     * Get the fully qualified class name of a spider.
     */
    public function getSpiderClass(string $name): string
    {
        $name = Str::studly($name);

        return $this->namespace.'\\'.$name;
    }

    /**
     * Get all spider files.
     */
    public function all(): array
    {
        if (! File::isDirectory($this->spidersPath)) {
            return [];
        }

        $files = File::files($this->spidersPath);
        $spiders = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $spiders[] = $file->getFilenameWithoutExtension();
            }
        }

        return $spiders;
    }

    /**
     * Load a spider class.
     */
    public function load(string $name): ?string
    {
        $class = $this->getSpiderClass($name);
        $path = $this->getSpiderPath($name);

        if (! File::exists($path)) {
            return null;
        }

        require_once $path;

        return class_exists($class) ? $class : null;
    }

    /**
     * Delete a spider.
     */
    public function delete(string $name): bool
    {
        $path = $this->getSpiderPath($name);

        if (! File::exists($path)) {
            return false;
        }

        return File::delete($path);
    }

    /**
     * Get spider information.
     */
    public function info(string $name): ?array
    {
        $path = $this->getSpiderPath($name);

        if (! File::exists($path)) {
            return null;
        }

        $content = File::get($path);

        // Extract start URLs using regex
        preg_match('/public array \$startUrls = \[(.*?)\];/s', $content, $matches);
        $startUrls = $matches[1] ?? '';

        // Extract middleware
        preg_match('/public array \$downloaderMiddleware = \[(.*?)\];/s', $content, $matches);
        $middleware = ! empty($matches[1]);

        // Extract item processors
        preg_match('/public array \$itemProcessors = \[(.*?)\];/s', $content, $matches);
        $processors = ! empty($matches[1]);

        return [
            'name' => $name,
            'path' => $path,
            'class' => $this->getSpiderClass($name),
            'has_middleware' => $middleware,
            'has_processors' => $processors,
            'size' => File::size($path),
            'modified' => File::lastModified($path),
        ];
    }
}
