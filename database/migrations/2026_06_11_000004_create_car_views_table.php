<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Logs every detail-page view so we can report views-per-day (B6)
     * and "X klanten bekeken deze auto vandaag" (F4).
     */
    public function up(): void
    {
        Schema::create('car_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_views');
    }
};
