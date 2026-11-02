<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'purchase_order_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'stock_before',
        'stock_after',
        'reason',
        'is_flagged',
        'canonical_representation',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
            'is_flagged' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Búsqueda de movimientos atípicos por vector
     */
    public function scopeSimilarVectors(Builder $query, array $vector, int $limit = 5): Builder
    {
        $vectorStr = '[' . implode(',', $vector) . ']';
        
        return $query->select('*')
            ->selectRaw('1 - (embedding <=> ?::vector) as cosine_similarity', [$vectorStr])
            ->whereNotNull('embedding')
            ->orderByRaw('embedding <=> ?::vector ASC', [$vectorStr])
            ->limit($limit);
    }

    public function updateEmbedding(array $vector): void
    {
        $vectorStr = '[' . implode(',', $vector) . ']';
        DB::statement('UPDATE inventory_movements SET embedding = ?::vector WHERE id = ?', [$vectorStr, $this->id]);
    }
}
