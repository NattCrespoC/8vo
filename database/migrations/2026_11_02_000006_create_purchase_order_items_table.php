<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2); // Precio unitario pactado en Bs.
            $table->decimal('subtotal', 14, 2);   // Subtotal en Bs.
            $table->decimal('historical_avg_price', 12, 2)->default(0.00); // Promedio histórico referencial
            $table->decimal('item_z_score', 8, 4)->nullable();             // Z-Score calculado para este ítem
            $table->boolean('is_anomalous')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
