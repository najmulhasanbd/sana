<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
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
     * @return BelongsTo<Division, Customer>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * @return BelongsTo<District, Customer>
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * @return BelongsTo<Zone, Customer>
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * @return HasMany<Sales>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('mobile', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('address', 'like', "%{$term}%")
              ->orWhereHas('division', function (Builder $div) use ($term): void {
                  $div->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('district', function (Builder $dist) use ($term): void {
                  $dist->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('zone', function (Builder $zn) use ($term): void {
                  $zn->where('name', 'like', "%{$term}%");
              });
        });
    }

    public function getTotalSalesAmountAttribute(): float
    {
        return (float) ($this->attributes['total_sales'] ?? $this->sales()->sum('total_amount'));
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return (float) ($this->attributes['total_paid'] ?? $this->sales()->sum('paid_amount'));
    }

    public function getTotalDueAmountAttribute(): float
    {
        return (float) ($this->attributes['total_due'] ?? $this->sales()->sum('due_amount'));
    }

    public function getLocationTextAttribute(): string
    {
        return collect([
            $this->zone?->name,
            $this->district?->name,
            $this->division?->name,
        ])->filter()->implode(', ') ?: ($this->address ?? 'ঠিকানা উল্লেখ নেই');
    }
}
