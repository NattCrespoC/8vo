<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_report_id',
        'found_report_id',
        'match_score',
        'geo_distance_km',
        'phenotype_score',
        'distance_score',
        'match_reasons',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'match_score' => 'float',
            'geo_distance_km' => 'float',
            'phenotype_score' => 'float',
            'distance_score' => 'float',
            'match_reasons' => 'array',
        ];
    }

    public function lostReport(): BelongsTo
    {
        return $this->belongsTo(PetReport::class, 'lost_report_id');
    }

    public function foundReport(): BelongsTo
    {
        return $this->belongsTo(PetReport::class, 'found_report_id');
    }

    public function getConfidenceBadgeAttribute(): array
    {
        if ($this->match_score >= 80.0) {
            return [
                'label' => 'ALTA COINCIDENCIA',
                'color' => 'emerald',
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            ];
        }

        if ($this->match_score >= 50.0) {
            return [
                'label' => 'COINCIDENCIA MEDIA',
                'color' => 'amber',
                'bg' => 'bg-amber-100 text-amber-800 border-amber-300',
            ];
        }

        return [
            'label' => 'BAJA COINCIDENCIA',
            'color' => 'gray',
            'bg' => 'bg-gray-100 text-gray-700 border-gray-300',
        ];
    }
}

