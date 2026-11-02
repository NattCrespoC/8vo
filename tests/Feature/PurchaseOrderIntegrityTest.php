<?php

declare(strict_types=1);

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

test('la base de datos contiene órdenes de compra anómalas con su respectiva resolución de auditoría', function () {
    $blockedOrders = PurchaseOrder::where('status', 'BLOQUEADO_POR_AUDITORIA')->with('resolution')->get();

    expect($blockedOrders->count())->toBeGreaterThanOrEqual(1);

    foreach ($blockedOrders as $order) {
        expect($order->audit_risk_level)->toBe('CRITICO')
            ->and($order->resolution)->not->toBeNull()
            ->and((float) $order->resolution->financial_impact_bs)->toBeGreaterThan(0.00);
    }
});

test('los usuarios oficiales Nataly Nicole Crespo y Lucio Mercado tienen los roles asignados', function () {
    $nataly = User::where('email', 'nataly.crespo@andina.bo')->first();
    $lucio = User::where('email', 'lucio.mercado@andina.bo')->first();

    expect($nataly)->not->toBeNull()
        ->and($nataly->hasRole('gerente'))->toBeTrue();

    expect($lucio)->not->toBeNull()
        ->and($lucio->hasRole('auditor'))->toBeTrue();
});
