<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spider_runs', function (Blueprint $table) {
            $table->id();
            $table->string('spider_name');
            $table->integer('items_scraped')->default(0);
            $table->integer('requests_sent')->default(0);
            $table->integer('errors_count')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('status'); // running, completed, failed
            $table->timestamps();

            $table->index('spider_name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spider_runs');
    }
};
