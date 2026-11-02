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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Operador de almacén
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('movement_type', 35)->index(); // INGRESO_COMPRA, SALIDA_VENTA, MERMA, AJUSTE_INVENTARIO
            $table->integer('quantity');                  // Positivo o negativo
            $table->decimal('unit_cost', 12, 2);          // Costo unitario en Bs.
            $table->decimal('total_cost', 14, 2);         // Costo total en Bs.
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->text('reason')->nullable();
            $table->boolean('is_flagged')->default(false)->index(); // Marcado por auditoría
            $table->text('canonical_representation')->nullable();
            $table->timestamps();
        });

        // Columna vectorial de 768 dimensiones para nomic-embed-text
        DB::statement('ALTER TABLE inventory_movements ADD COLUMN embedding vector(768);');

        // Índice HNSW con métrica de Distancia de Coseno
        DB::statement('CREATE INDEX idx_inv_embedding_hnsw ON inventory_movements USING hnsw (embedding vector_cosine_ops);');
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
