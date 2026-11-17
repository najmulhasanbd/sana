<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'district_id',
        'name',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'district_id' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the district that owns the zone.
     *
     * @return BelongsTo<District, Zone>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Scope query to active zones.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope query by search term.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$term}%");
    }
}
