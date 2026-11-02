<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Audit\CanonicalDataFormatter;

beforeEach(function () {
    $this->formatter = new CanonicalDataFormatter();
    $this->user = User::first();
    $this->supplier = Supplier::first();
    $this->product = Product::first();
});

test('formatea la cadena canónica según el contrato obligatorio de AGENTS.md', function () {
    $order = PurchaseOrder::create([
        'order_number' => uniqid('OC-CANONICAL-'),
        'supplier_id' => $this->supplier->id,
        'user_id' => $this->user->id,
        'total_amount' => 420.00,
        'status' => 'PENDIENTE_AUDITORIA',
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'unit_price' => 210.00,
        'subtotal' => 420.00,
    ]);

    $order->load(['supplier.category', 'items.product']);
    $canonicalStr = $this->formatter->formatPurchaseOrder($order, 'compras');

    expect($canonicalStr)
        ->toContain('TIPO:ORDEN_COMPRA')
        ->toContain('PROVEEDOR:' . strtoupper($this->supplier->business_name))
        ->toContain('TOTAL:420.00')
        ->toContain('ROL:COMPRAS')
        ->toContain($this->product->sku . ':cant=2,pu=210.00');
});
