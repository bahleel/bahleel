<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapedItem extends Model
{
    protected $fillable = [
        'spider_name',
        'spider_run_id',
        'data',
        'url',
        'hash',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the spider run that owns the scraped item.
     */
    public function spiderRun(): BelongsTo
    {
        return $this->belongsTo(SpiderRun::class);
    }

    /**
     * Generate hash for duplicate detection.
     */
    public static function generateHash(array $data, ?string $url = null): string
    {
        $content = json_encode($data).($url ?? '');

        return hash('sha256', $content);
    }
}
