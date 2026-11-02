<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;

it('updates weighted average cost and standard deviation upon purchase', function () {
    $category = Category::first();

    // 1. Crear producto con stock inicial de 10 unidades a 100.00 Bs. cada una (Valor = 1000 Bs.)
    $product = Product::create([
        'category_id' => $category?->id,
        'sku' => uniqid('SKU-TEST-'),
        'name' => 'Disco de Corte Diamantado 7 Pulgadas',
        'current_stock' => 10,
        'weighted_avg_cost' => 100.00,
        'std_dev_cost' => 5.00,
        'last_purchase_price' => 100.00,
    ]);

    // 2. Ingresar compra de 10 unidades a 120.00 Bs. (Nueva valoracion total = 1000 + 1200 = 2200 Bs.)
    // Nuevo CPP esperado = 2200 / 20 = 110.00 Bs.
    $product->updateCostsUponPurchase(10, 120.00);

    $product->refresh();

    expect($product->current_stock)->toBe(20)
        ->and((float) $product->weighted_avg_cost)->toBe(110.00)
        ->and((float) $product->last_purchase_price)->toBe(120.00)
        ->and((float) $product->std_dev_cost)->toBeGreaterThan(0.00);
});
