<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'expense_category_id',
        'date',
        'amount',
        'payment_method',
        'title',
        'note',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expense_category_id' => 'integer',
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the category that owns this expense.
     *
     * @return BelongsTo<ExpenseCategory, Expense>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    /**
     * Scope query by search term.
     *
     * @param  Builder<Expense>  $query
     * @param  string|null  $term
     * @return Builder<Expense>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('note', 'like', "%{$term}%")
                ->orWhere('payment_method', 'like', "%{$term}%")
                ->orWhereHas('category', function (Builder $catQuery) use ($term): void {
                    $catQuery->where('name', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Scope query between two dates.
     *
     * @param  Builder<Expense>  $query
     * @param  string|null  $fromDate
     * @param  string|null  $toDate
     * @return Builder<Expense>
     */
    public function scopeDateBetween(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        if (! empty($fromDate) && ! empty($toDate)) {
            return $query->whereBetween('date', [$fromDate, $toDate]);
        }

        if (! empty($fromDate)) {
            return $query->where('date', '>=', $fromDate);
        }

        if (! empty($toDate)) {
            return $query->where('date', '<=', $toDate);
        }

        return $query;
    }
}
