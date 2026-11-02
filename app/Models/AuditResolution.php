<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditResolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'auditor_user_id',
        'risk_level',
        'confidence_percentage',
        'primary_reason',
        'financial_impact_bs',
        'recommended_action',
        'status',
        'resolution_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence_percentage' => 'integer',
            'financial_impact_bs' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function auditorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_user_id');
    }
}
