<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AuditResolution;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Audit\CanonicalDataFormatter;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $userCompras = User::where('email', 'compras@andina.bo')->first();
        $userAlmacen = User::where('email', 'almacen@andina.bo')->first();
        $userAuditor = User::where('email', 'lucio.mercado@andina.bo')->first();
        $formatter = new CanonicalDataFormatter();

        $normalSuppliers = Supplier::where('risk_score', '<', 20)->get();
        $riskySupplier = Supplier::where('risk_score', '>', 50)->first();
        $products = Product::all();

        if ($products->isEmpty() || $normalSuppliers->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->subMonths(24);
        $orderCounter = 1;

        DB::beginTransaction();

        // ================================================================
        // 1. ESTADO: APROBADO (460 Órdenes Comerciales Históricas)
        // ================================================================
        $this->command?->info('Sembrando 460 compras históricas en estado APROBADO...');

        for ($i = 1; $i <= 460; $i++) {
            $supplier = $normalSuppliers->random();
            $orderDate = (clone $startDate)->addMinutes($i * 2200)->setHour(rand(8, 17))->setMinute(rand(0, 59));
            $orderNumber = sprintf('OC-%s-%04d', $orderDate->format('Y'), $orderCounter++);

            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $userCompras->id,
                'total_amount' => 0.00,
                'status' => 'APROBADO',
                'audit_risk_level' => 'BAJO',
                'z_score_max' => 0.35,
                'cosine_similarity_min' => 0.94,
                'notes' => 'Adquisición rutinaria de abastecimiento programado.',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $numItems = rand(1, 3);
            $selectedProducts = $products->random($numItems);
            $orderTotal = 0.00;

            foreach ($selectedProducts as $prod) {
                $qty = rand(5, 25);
                $fluctuation = (rand(-25, 25) / 1000.0) * (float) $prod->weighted_avg_cost;
                $unitPrice = round((float) $prod->weighted_avg_cost + $fluctuation, 2);
                $subtotal = round($unitPrice * $qty, 2);
                $orderTotal += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'historical_avg_price' => $prod->weighted_avg_cost,
                    'item_z_score' => $prod->calculateZScore($unitPrice),
                    'is_anomalous' => false,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Movimiento INGRESO_COMPRA
                InventoryMovement::create([
                    'product_id' => $prod->id,
                    'user_id' => $userAlmacen->id,
                    'purchase_order_id' => $order->id,
                    'movement_type' => 'INGRESO_COMPRA',
                    'quantity' => $qty,
                    'unit_cost' => $unitPrice,
                    'total_cost' => $subtotal,
                    'stock_before' => $prod->current_stock,
                    'stock_after' => $prod->current_stock + $qty,
                    'reason' => "Ingreso físico conforme según {$orderNumber}",
                    'is_flagged' => false,
                    'created_at' => (clone $orderDate)->addHours(48),
                    'updated_at' => (clone $orderDate)->addHours(48),
                ]);

                // Movimiento SALIDA_VENTA posterior aleatoria
                if (rand(1, 10) > 4) {
                    $saleQty = rand(2, min($qty, 10));
                    InventoryMovement::create([
                        'product_id' => $prod->id,
                        'user_id' => $userAlmacen->id,
                        'purchase_order_id' => null,
                        'movement_type' => 'SALIDA_VENTA',
                        'quantity' => -$saleQty,
                        'unit_cost' => $unitPrice,
                        'total_cost' => round($unitPrice * $saleQty, 2),
                        'stock_before' => $prod->current_stock + $qty,
                        'stock_after' => ($prod->current_stock + $qty) - $saleQty,
                        'reason' => "Despacho a distribuidor mayorista regional",
                        'is_flagged' => false,
                        'created_at' => (clone $orderDate)->addDays(4),
                        'updated_at' => (clone $orderDate)->addDays(4),
                    ]);
                }
            }

            $order->total_amount = $orderTotal;
            $order->canonical_representation = $formatter->formatPurchaseOrder($order, 'compras');
            $order->save();
        }

        // ================================================================
        // 2. ESTADO: BORRADOR (15 Órdenes de Compra)
        // ================================================================
        $this->command?->info('Sembrando 15 órdenes en estado BORRADOR...');

        for ($b = 1; $b <= 15; $b++) {
            $supplier = $normalSuppliers->random();
            $orderNumber = sprintf('OC-DRAFT-%04d', $b);

            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $userCompras->id,
                'total_amount' => 0.00,
                'status' => 'BORRADOR',
                'audit_risk_level' => 'BAJO',
                'z_score_max' => 0.10,
                'notes' => 'Cotización preliminar en borrador.',
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);

            $prod = $products->random();
            $qty = rand(5, 15);
            $unitPrice = (float) $prod->weighted_avg_cost;
            $subtotal = round($unitPrice * $qty, 2);

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'historical_avg_price' => $prod->weighted_avg_cost,
                'item_z_score' => 0.00,
                'is_anomalous' => false,
            ]);

            $order->total_amount = $subtotal;
            $order->canonical_representation = $formatter->formatPurchaseOrder($order, 'compras');
            $order->save();
        }

        // ================================================================
        // 3. ESTADO: PENDIENTE_AUDITORIA (15 Órdenes de Compra)
        // ================================================================
        $this->command?->info('Sembrando 15 órdenes en estado PENDIENTE_AUDITORIA...');

        for ($p = 1; $p <= 15; $p++) {
            $supplier = $normalSuppliers->random();
            $orderNumber = sprintf('OC-PEND-%04d', $p);

            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $userCompras->id,
                'total_amount' => 0.00,
                'status' => 'PENDIENTE_AUDITORIA',
                'audit_risk_level' => 'MEDIO',
                'z_score_max' => 1.65,
                'notes' => 'En cola asíncrona de vectorización e inferencia.',
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
                'updated_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);

            $prod = $products->random();
            $qty = rand(10, 20);
            $unitPrice = round((float) $prod->weighted_avg_cost * 1.12, 2);
            $subtotal = round($unitPrice * $qty, 2);

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'historical_avg_price' => $prod->weighted_avg_cost,
                'item_z_score' => 1.65,
                'is_anomalous' => false,
            ]);

            $order->total_amount = $subtotal;
            $order->canonical_representation = $formatter->formatPurchaseOrder($order, 'compras');
            $order->save();
        }

        // ================================================================
        // 4. ESTADO: ANULADO (15 Órdenes de Compra)
        // ================================================================
        $this->command?->info('Sembrando 15 órdenes en estado ANULADO...');

        for ($a = 1; $a <= 15; $a++) {
            $supplier = $normalSuppliers->random();
            $orderNumber = sprintf('OC-ANUL-%04d', $a);

            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $userCompras->id,
                'total_amount' => 0.00,
                'status' => 'ANULADO',
                'audit_risk_level' => 'BAJO',
                'notes' => 'Orden cancelada a solicitud del proveedor por falta de stock.',
                'created_at' => Carbon::now()->subDays(rand(15, 90)),
                'updated_at' => Carbon::now()->subDays(rand(15, 90)),
            ]);

            $prod = $products->random();
            $qty = rand(4, 10);
            $unitPrice = (float) $prod->weighted_avg_cost;
            $subtotal = round($unitPrice * $qty, 2);

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'historical_avg_price' => $prod->weighted_avg_cost,
                'item_z_score' => 0.00,
                'is_anomalous' => false,
            ]);

            $order->total_amount = $subtotal;
            $order->canonical_representation = $formatter->formatPurchaseOrder($order, 'compras');
            $order->save();
        }

        // ================================================================
        // 5. ESTADO: BLOQUEADO_POR_AUDITORIA (30 Órdenes con Resoluciones en 3 Estados)
        // ================================================================
        $this->command?->info('Sembrando 30 compras anómalas con resoluciones (PENDIENTE, APROBADO_CON_JUSTIFICACION, ORDEN_ANULADA)...');

        $resolutionStates = [
            'PENDIENTE_RESOLUCION',
            'APROBADO_CON_JUSTIFICACION',
            'ORDEN_ANULADA',
        ];

        for ($j = 1; $j <= 30; $j++) {
            $resState = $resolutionStates[$j % 3];
            $supplier = ($j % 2 === 0 && $riskySupplier) ? $riskySupplier : $normalSuppliers->random();
            $anomDate = Carbon::now()->subDays(rand(2, 120))->setHour(rand(20, 23))->setMinute(rand(10, 50));
            $orderNumber = sprintf('OC-ANOM-%04d', $j);

            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'user_id' => $userCompras->id,
                'total_amount' => 0.00,
                'status' => 'BLOQUEADO_POR_AUDITORIA',
                'audit_risk_level' => 'CRITICO',
                'z_score_max' => 3.85,
                'cosine_similarity_min' => 0.62,
                'notes' => 'Retención preventiva automática: detección de desvío crítico de precios.',
                'created_at' => $anomDate,
                'updated_at' => $anomDate,
            ]);

            $prod = $products->random();
            $qty = rand(15, 35);
            $markupFactor = 1.42; // Sobreprecio del 42%
            $inflatedPrice = round((float) $prod->weighted_avg_cost * $markupFactor, 2);
            $subtotal = round($inflatedPrice * $qty, 2);
            $diffTotal = ($inflatedPrice - (float) $prod->weighted_avg_cost) * $qty;

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $inflatedPrice,
                'subtotal' => $subtotal,
                'historical_avg_price' => $prod->weighted_avg_cost,
                'item_z_score' => $prod->calculateZScore($inflatedPrice),
                'is_anomalous' => true,
                'created_at' => $anomDate,
                'updated_at' => $anomDate,
            ]);

            $order->total_amount = $subtotal;
            $order->canonical_representation = $formatter->formatPurchaseOrder($order, 'compras');
            $order->save();

            // Dictamen de Auditoría con estado asignado
            AuditResolution::create([
                'purchase_order_id' => $order->id,
                'auditor_user_id' => $userAuditor?->id,
                'risk_level' => 'CRITICO',
                'confidence_percentage' => rand(92, 98),
                'primary_reason' => "Sobreprecio crítico del 42% en {$prod->name} y registro nocturno no autorizado.",
                'financial_impact_bs' => round($diffTotal, 2),
                'recommended_action' => 'Bloquear orden de pago y exigir tres cotizaciones de respaldo.',
                'status' => $resState,
                'resolution_notes' => match ($resState) {
                    'APROBADO_CON_JUSTIFICACION' => 'Aprobado excepcionalmente por Gerencia General ante desabastecimiento de urgencia para obra.',
                    'ORDEN_ANULADA' => 'Se ratifica el bloqueo y se cancela la orden de compra definitivamente.',
                    default => 'Pendiente de descargo por el oficial de compras.',
                },
                'resolved_at' => $resState !== 'PENDIENTE_RESOLUCION' ? (clone $anomDate)->addDays(1) : null,
                'created_at' => $anomDate,
                'updated_at' => $anomDate,
            ]);
        }

        // ================================================================
        // 6. MOVIMIENTOS DE ALMACÉN: MERMA Y AJUSTE_INVENTARIO
        // ================================================================
        $this->command?->info('Sembrando movimientos de MERMA y AJUSTE_INVENTARIO...');

        foreach ($products->take(6) as $pMerma) {
            // Merma
            InventoryMovement::create([
                'product_id' => $pMerma->id,
                'user_id' => $userAlmacen->id,
                'purchase_order_id' => null,
                'movement_type' => 'MERMA',
                'quantity' => -2,
                'unit_cost' => $pMerma->weighted_avg_cost,
                'total_cost' => round((float) $pMerma->weighted_avg_cost * 2, 2),
                'stock_before' => $pMerma->current_stock,
                'stock_after' => max(0, $pMerma->current_stock - 2),
                'reason' => 'Deterioro por manipulación en estantería de almacén central',
                'is_flagged' => true,
                'created_at' => Carbon::now()->subDays(rand(5, 45)),
            ]);

            // Ajuste de inventario
            InventoryMovement::create([
                'product_id' => $pMerma->id,
                'user_id' => $userAlmacen->id,
                'purchase_order_id' => null,
                'movement_type' => 'AJUSTE_INVENTARIO',
                'quantity' => 1,
                'unit_cost' => $pMerma->weighted_avg_cost,
                'total_cost' => (float) $pMerma->weighted_avg_cost,
                'stock_before' => $pMerma->current_stock,
                'stock_after' => $pMerma->current_stock + 1,
                'reason' => 'Conciliación física en recuento trimestral de inventario',
                'is_flagged' => false,
                'created_at' => Carbon::now()->subDays(rand(10, 60)),
            ]);
        }

        DB::commit();
        $this->command?->info('✅ Base de datos poblada exhaustivamente con TODOS los estados.');
    }
}
