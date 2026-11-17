<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'division_id',
        'district_id',
        'zone_id',
        'address',
        'note',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'division_id' => 'integer',
            'district_id' => 'integer',
            'zone_id' => 'integer',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the division that the supplier belongs to.
     *
     * @return BelongsTo<Division, Supplier>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the district that the supplier belongs to.
     *
     * @return BelongsTo<District, Supplier>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the zone that the supplier belongs to.
     *
     * @return BelongsTo<Zone, Supplier>
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to search suppliers by name, mobile, email, address, or locations.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('mobile', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('address', 'like', "%{$term}%")
              ->orWhereHas('division', function (Builder $div) use ($term) {
                  $div->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('district', function (Builder $dist) use ($term) {
                  $dist->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('zone', function (Builder $zn) use ($term) {
                  $zn->where('name', 'like', "%{$term}%");
              });
        });
    }
}
