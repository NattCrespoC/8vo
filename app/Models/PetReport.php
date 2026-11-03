<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PetReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'species',
        'breed',
        'size',
        'primary_color',
        'secondary_color',
        'special_features',
        'address_description',
        'latitude',
        'longitude',
        'incident_date',
        'contact_name',
        'contact_phone',
        'contact_whatsapp',
        'reward_amount',
        'status',
        'primary_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'incident_date' => 'date',
            'reward_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PetPhoto::class);
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(PetPhoto::class)->where('is_primary', true);
    }

    public function matchesAsLost(): HasMany
    {
        return $this->hasMany(PetMatch::class, 'lost_report_id');
    }

    public function matchesAsFound(): HasMany
    {
        return $this->hasMany(PetMatch::class, 'found_report_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'ACTIVO');
    }

    public function scopeLost(Builder $query): Builder
    {
        return $query->where('type', 'PERDIDA');
    }

    public function scopeFound(Builder $query): Builder
    {
        return $query->where('type', 'ENCONTRADA');
    }

    public function isLost(): bool
    {
        return $this->type === 'PERDIDA';
    }

    public function isFound(): bool
    {
        return $this->type === 'ENCONTRADA';
    }

    public function getEffectivePhotoUrlAttribute(): string
    {
        if ($this->primary_photo_url) {
            return $this->primary_photo_url;
        }

        $photo = $this->photos()->first();
        if ($photo) {
            return asset('storage/' . $photo->photo_path);
        }

        return $this->species === 'GATO' 
            ? 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=600&auto=format&fit=crop'
            : 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=600&auto=format&fit=crop';
    }
}

