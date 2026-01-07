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
        Schema::create('spider_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spider_run_id')->constrained()->onDelete('cascade');
            $table->string('level'); // info, warning, error
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index('spider_run_id');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spider_logs');
    }
};
