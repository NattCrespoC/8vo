<?php

declare(strict_types=1);

namespace App\Services\Audit;

use App\Models\PurchaseOrder;
use Carbon\Carbon;

class CanonicalDataFormatter
{
    /**
     * Construye la representación canónica estricta para una orden de compra
     * Contrato: TIPO:{TIPO}|PROVEEDOR:{PROV}|RUBRO:{RUBRO}|TOTAL:{TOTAL}|HORA:{HH}|DIA:{1-7}|ROL:{ROL}|ITEMS:{ITEMS}
     */
    public function formatPurchaseOrder(PurchaseOrder $order, string $operatorRole = 'compras'): string
    {
        $supplier = $order->supplier;
        $category = $supplier?->category?->name ?? 'GENERAL';
        $providerName = $supplier?->business_name ?? 'PROVEEDOR_DESCONOCIDO';
        $createdAt = $order->created_at ? Carbon::parse($order->created_at) : Carbon::now();
        
        $itemsStr = [];
        foreach ($order->items as $item) {
            $sku = $item->product?->sku ?? "PROD-{$item->product_id}";
            $itemsStr[] = sprintf('%s:cant=%d,pu=%.2f', $sku, $item->quantity, (float) $item->unit_price);
        }

        return sprintf(
            'TIPO:ORDEN_COMPRA|PROVEEDOR:%s|RUBRO:%s|TOTAL:%.2f|HORA:%02d|DIA:%d|ROL:%s|ITEMS:%s',
            strtoupper(trim($providerName)),
            strtoupper(trim($category)),
            (float) $order->total_amount,
            (int) $createdAt->format('H'),
            (int) $createdAt->format('N'), // 1 (Lunes) a 7 (Domingo)
            strtoupper(trim($operatorRole)),
            implode(';', $itemsStr)
        );
    }
}
