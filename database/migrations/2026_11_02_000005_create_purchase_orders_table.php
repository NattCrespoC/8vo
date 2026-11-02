<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->index();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Operador de compras
            $table->decimal('total_amount', 14, 2)->default(0.00); // Monto Total en Bolivianos (Bs.)
            $table->string('status', 35)->default('PENDIENTE_AUDITORIA')->index(); 
            // BORRADOR, PENDIENTE_AUDITORIA, APROBADO, BLOQUEADO_POR_AUDITORIA, ANULADO
            $table->string('audit_risk_level', 20)->default('BAJO')->index();
            // BAJO, MEDIO, CRITICO
            $table->decimal('z_score_max', 8, 4)->nullable();
            $table->decimal('cosine_similarity_min', 8, 4)->nullable();
            $table->text('canonical_representation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Columna vectorial de 768 dimensiones para nomic-embed-text
        DB::statement('ALTER TABLE purchase_orders ADD COLUMN embedding vector(768);');

        // Índice HNSW con métrica de Distancia de Coseno
        DB::statement('CREATE INDEX idx_po_embedding_hnsw ON purchase_orders USING hnsw (embedding vector_cosine_ops);');
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
