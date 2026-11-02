<?php

declare(strict_types=1);

namespace App\Services\Audit;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class AnomalyDetectionService
{
    public const UMBRAL_Z_CRITICO = 2.20;
    public const UMBRAL_Z_MEDIO = 1.50;
    public const UMBRAL_COSENO_ANOMALO = 0.80;

    /**
     * Evalúa una orden de compra y sus ítems determinando el nivel de riesgo y si debe bloquearse
     *
     * @return array{
     *     is_anomalous: bool,
     *     risk_level: string,
     *     max_z_score: float,
     *     should_block: bool,
     *     primary_reason: string,
     *     financial_impact_bs: float
     * }
     */
    public function evaluatePurchaseOrder(PurchaseOrder $order): array
    {
        $maxZScore = 0.0;
        $totalFinancialImpact = 0.0;
        $anomalousReasons = [];
        $isAnomalous = false;

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            $unitPrice = (float) $item->unit_price;
            $avgPrice = (float) $product->weighted_avg_cost;
            $zScore = $product->calculateZScore($unitPrice);
            
            // Actualizar métricas en el ítem
            $item->historical_avg_price = $avgPrice;
            $item->item_z_score = $zScore;
            
            if ($zScore > $maxZScore) {
                $maxZScore = $zScore;
            }

            if ($zScore >= self::UMBRAL_Z_CRITICO) {
                $item->is_anomalous = true;
                $isAnomalous = true;
                
                $diffPerUnit = max(0.0, $unitPrice - $avgPrice);
                $impactItem = $diffPerUnit * $item->quantity;
                $totalFinancialImpact += $impactItem;

                $percentOver = $avgPrice > 0 ? (($unitPrice - $avgPrice) / $avgPrice) * 100 : 100;
                $anomalousReasons[] = sprintf(
                    'Sobreprecio del %.1f%% en ítem %s (PU: %.2f Bs. vs Media Histórica: %.2f Bs. | Z=%.2f)',
                    $percentOver,
                    $product->name,
                    $unitPrice,
                    $avgPrice,
                    $zScore
                );
            } else {
                $item->is_anomalous = false;
            }

            $item->save();
        }

        // Determinar nivel de riesgo general
        if ($maxZScore >= self::UMBRAL_Z_CRITICO) {
            $riskLevel = 'CRITICO';
            $shouldBlock = true;
        } elseif ($maxZScore >= self::UMBRAL_Z_MEDIO) {
            $riskLevel = 'MEDIO';
            $shouldBlock = false;
        } else {
            $riskLevel = 'BAJO';
            $shouldBlock = false;
        }

        $reason = count($anomalousReasons) > 0 
            ? implode('; ', $anomalousReasons) 
            : 'Transacción comercial dentro de los parámetros estadísticos históricos normales.';

        return [
            'is_anomalous' => $isAnomalous,
            'risk_level' => $riskLevel,
            'max_z_score' => round($maxZScore, 4),
            'should_block' => $shouldBlock,
            'primary_reason' => $reason,
            'financial_impact_bs' => round($totalFinancialImpact, 2),
        ];
    }

    /**
     * Calcula la Similitud de Coseno matemática pura entre dos vectores de dimensiones iguales
     */
    public function calculateCosineSimilarity(array $vecA, array $vecB): float
    {
        $count = count($vecA);
        if ($count === 0 || $count !== count($vecB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $a = (float) $vecA[$i];
            $b = (float) $vecB[$i];
            $dotProduct += $a * $b;
            $normA += $a * $a;
            $normB += $b * $b;
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
