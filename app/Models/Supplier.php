<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nit',
        'business_name',
        'trade_name',
        'contact_name',
        'phone',
        'email',
        'city',
        'address',
        'category_id',
        'risk_score',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'risk_score' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Calcula la calificación de riesgo histórica inicial / por defecto del proveedor
     */
    public function calculateDefaultRisk(): float
    {
        $baseRisk = 5.00;

        // Si no tiene categoría formal asignada
        if (!$this->category_id) {
            $baseRisk += 30.00;
        }

        // Si no tiene contacto formal o correo corporativo
        if (empty($this->contact_name) || empty($this->email)) {
            $baseRisk += 15.00;
        }

        // Si el NIT es atípico o sospechoso (ej. empieza con 999)
        if (str_starts_with((string) $this->nit, '999')) {
            $baseRisk += 40.00;
        }

        return min(100.00, round($baseRisk, 2));
    }
}
