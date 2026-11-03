<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
        } catch (\Throwable $e) {
            // Permitir continuidad si la extensión no está disponible en el host local
        }
    }

    public function down(): void
    {
        // No desinstalamos la extensión para evitar romper otras tablas compartidas
    }
};
