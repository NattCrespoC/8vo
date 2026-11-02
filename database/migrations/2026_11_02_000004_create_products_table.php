<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('sku', 50)->unique()->index();
            $table->string('name', 200)->index();
            $table->text('description')->nullable();
            $table->string('unit_of_measure', 20)->default('PZA'); // PZA, MTR, KG, CJ, GLN
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(10);
            $table->decimal('weighted_avg_cost', 12, 2)->default(0.00); // Costo Promedio Ponderado en Bs.
            $table->decimal('std_dev_cost', 12, 2)->default(0.00);      // Desviación Estándar de Costo en Bs.
            $table->decimal('last_purchase_price', 12, 2)->default(0.00);
            $table->decimal('standard_sale_price', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
