<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_report_id')->constrained('pet_reports')->cascadeOnDelete();
            $table->foreignId('found_report_id')->constrained('pet_reports')->cascadeOnDelete();
            $table->decimal('match_score', 5, 2);
            $table->decimal('geo_distance_km', 8, 2);
            $table->decimal('phenotype_score', 5, 2);
            $table->decimal('distance_score', 5, 2);
            $table->json('match_reasons')->nullable();
            $table->enum('status', ['SUGERIDO', 'DESCARTADO', 'CONFIRMADO'])->default('SUGERIDO');
            $table->timestamps();

            $table->unique(['lost_report_id', 'found_report_id']);
            $table->index('match_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_matches');
    }
};

