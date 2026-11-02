<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Audit\AnomalyDetectionService;

beforeEach(function () {
    $this->service = new AnomalyDetectionService();
    $this->user = User::first();
    $this->supplier = Supplier::first();
    $this->product = Product::first();
});

test('clasifica como BAJO y aprobada una compra con precio normal histórico', function () {
    $order = PurchaseOrder::create([
        'order_number' => uniqid('OC-TEST-NORM-'),
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'total_amount' => 420.00,
        'status' => 'PENDIENTE_AUDITORIA',
    ]);

    $item = PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'unit_price' => (float) $this->product->weighted_avg_cost,
        'subtotal' => (float) $this->product->weighted_avg_cost,
    ]);

    $order->load('items.product');
    $evaluation = $this->service->evaluatePurchaseOrder($order);

    expect($evaluation['is_anomalous'])->toBeFalse()
        ->and($evaluation['risk_level'])->toBe('BAJO')
        ->and($evaluation['should_block'])->toBeFalse()
        ->and($evaluation['financial_impact_bs'])->toEqual(0.00);
});

test('clasifica como CRITICO y retiene preventivamente una compra con sobreprecio del 40%', function () {
    $order = PurchaseOrder::create([
        'order_number' => uniqid('OC-TEST-CRIT-'),
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'total_amount' => 1000.00,
        'status' => 'PENDIENTE_AUDITORIA',
    ]);

    $inflatedPrice = round((float) $this->product->weighted_avg_cost * 1.40, 2);

    $item = PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 10,
        'unit_price' => $inflatedPrice,
        'subtotal' => $inflatedPrice * 10,
    ]);

    $order->load('items.product');
    $evaluation = $this->service->evaluatePurchaseOrder($order);

    expect($evaluation['is_anomalous'])->toBeTrue()
        ->and($evaluation['risk_level'])->toBe('CRITICO')
        ->and($evaluation['should_block'])->toBeTrue()
        ->and($evaluation['max_z_score'])->toBeGreaterThanOrEqual(2.20)
        ->and($evaluation['financial_impact_bs'])->toBeGreaterThan(0.00);
});

test('calcula correctamente la similitud de coseno vectorial', function () {
    $vecA = [1.0, 0.0, 0.0];
    $vecB = [1.0, 0.0, 0.0];
    $vecC = [0.0, 1.0, 0.0];

    expect($this->service->calculateCosineSimilarity($vecA, $vecB))->toEqual(1.0);
    expect($this->service->calculateCosineSimilarity($vecA, $vecC))->toEqual(0.0);
});
