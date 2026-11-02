<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'supplier_id',
        'user_id',
        'total_amount',
        'status',
        'audit_risk_level',
        'z_score_max',
        'cosine_similarity_min',
        'canonical_representation',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'z_score_max' => 'decimal:4',
            'cosine_similarity_min' => 'decimal:4',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function resolution(): HasOne
    {
        return $this->hasOne(AuditResolution::class);
    }

    /**
     * Scope para encontrar órdenes similares usando pgvector (Distancia de Coseno <=>)
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

    /**
     * Asignar vector nomic-embed-text (768 dimensiones) directamente a la base de datos
     */
    public function updateEmbedding(array $vector): void
    {
        $vectorStr = '[' . implode(',', $vector) . ']';
        DB::statement('UPDATE purchase_orders SET embedding = ?::vector WHERE id = ?', [$vectorStr, $this->id]);
    }
}
