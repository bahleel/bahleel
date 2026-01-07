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
        Schema::create('scraped_items', function (Blueprint $table) {
            $table->id();
            $table->string('spider_name');
            $table->foreignId('spider_run_id')->constrained()->onDelete('cascade');
            $table->json('data');
            $table->string('url')->nullable();
            $table->string('hash')->unique();
            $table->timestamps();

            $table->index('spider_name');
            $table->index('spider_run_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_items');
    }
};
