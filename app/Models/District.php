<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'division_id',
        'name',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'division_id' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the division that owns the district.
     *
     * @return BelongsTo<Division, District>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get all zones under this district.
     *
     * @return HasMany<Zone>
     */
    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    /**
     * Scope query to active districts.
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
