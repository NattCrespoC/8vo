<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'description',
        'unit_of_measure',
        'current_stock',
        'min_stock',
        'weighted_avg_cost',
        'std_dev_cost',
        'last_purchase_price',
        'standard_sale_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock' => 'integer',
            'weighted_avg_cost' => 'decimal:2',
            'std_dev_cost' => 'decimal:2',
            'last_purchase_price' => 'decimal:2',
            'standard_sale_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Calcula si un precio unitario propuesto es anómalo según el Z-Score
     */
    public function calculateZScore(float $proposedPrice): float
    {
        $avg = (float) $this->weighted_avg_cost;
        $stdDev = (float) $this->std_dev_cost;

        // Si la desviación estándar es 0 o no representativa, usar umbral proporcional del 10%
        if ($stdDev <= 0.001) {
            $stdDev = $avg > 0 ? $avg * 0.10 : 1.0;
        }

        return ($proposedPrice - $avg) / $stdDev;
    }

    /**
     * Actualiza automáticamente el Costo Promedio Ponderado (CPP),
     * la desviación estándar y el stock del producto tras una compra.
     */
    public function updateCostsUponPurchase(int $newQty, float $newUnitPrice): void
    {
        if ($newQty <= 0) {
            return;
        }

        $prevStock = (int) $this->current_stock;
        $prevCpp = (float) $this->weighted_avg_cost;
        $prevStdDev = (float) $this->std_dev_cost;

        $totalStock = $prevStock + $newQty;

        // Si el stock anterior era 0 o negativo, el nuevo CPP es directamente el precio de compra
        if ($prevStock <= 0 || $prevCpp <= 0.0) {
            $newCpp = $newUnitPrice;
            $newStdDev = round($newUnitPrice * 0.05, 2); // 5% base inicial
        } else {
            // Fórmula contable canónica del Costo Promedio Ponderado (CPP)
            $totalValuation = ($prevStock * $prevCpp) + ($newQty * $newUnitPrice);
            $newCpp = round($totalValuation / $totalStock, 2);

            // Actualización de varianza y desviación estándar combinada
            $varPrev = pow($prevStdDev, 2);
            $varNewDiff = pow($newUnitPrice - $newCpp, 2);
            $varAdjusted = (($prevStock * $varPrev) + ($newQty * $varNewDiff)) / $totalStock;
            $newStdDev = round(sqrt(max(0.01, $varAdjusted)), 2);
        }

        $this->current_stock = $totalStock;
        $this->weighted_avg_cost = $newCpp;
        $this->std_dev_cost = $newStdDev;
        $this->last_purchase_price = $newUnitPrice;
        $this->save();
    }
}
