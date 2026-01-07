<?php

namespace App\Exporters;

use Illuminate\Support\Collection;

interface ExporterInterface
{
    /**
     * Export data to the specified format.
     *
     * @param  Collection  $data  The data to export
     * @param  string  $outputPath  The output file path
     * @return bool Success status
     */
    public function export(Collection $data, string $outputPath): bool;

    /**
     * Get the file extension for this exporter.
     *
     * @return string File extension without dot (e.g., 'csv', 'json')
     */
    public function getExtension(): string;
}
