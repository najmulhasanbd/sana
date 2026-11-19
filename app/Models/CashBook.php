<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBook extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'category',
        'date',
        'amount',
        'payment_method',
        'reference_no',
        'note',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'created_by' => 'integer',
    ];

    /**
     * Default presets for common categories.
     *
     * @return array<string, list<string>>
     */
    public static function defaultCategories(): array
    {
        return [
            'deposit' => [
                'মূলধন / নিজস্ব তহবিল জমা',
                'ব্যাংক থেকে উত্তোলন',
                'হাতে নগদ জমা',
                'অন্যান্য প্রাপ্তি / জমা',
            ],
            'expense' => [
                'লেবার / শ্রমিক খরচ',
                'বিদ্যুৎ ও জ্বালানি বিল',
                'মাছের খাবার ক্রয়',
                'গাড়ি ভাড়া ও পরিবহন',
                'যন্ত্রপাতি ও হ্যাচারি মেরামত',
                'অফিস ও স্টেশনারি',
                'আপ্যায়ন ও চা-নাস্তা',
                'ব্যক্তিগত খরচ / উত্তোলন',
                'বিবিধ খরচ',
            ],
        ];
    }

    /**
     * Get the user who recorded this transaction.
     *
     * @return BelongsTo<User, CashBook>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if transaction is a deposit.
     */
    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    /**
     * Check if transaction is an expense.
     */
    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    /**
     * Scope to deposits only.
     *
     * @param Builder<CashBook> $query
     * @return Builder<CashBook>
     */
    public function scopeDeposits(Builder $query): Builder
    {
        return $query->where('type', 'deposit');
    }

    /**
     * Scope to expenses only.
     *
     * @param Builder<CashBook> $query
     * @return Builder<CashBook>
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope by search term.
     *
     * @param Builder<CashBook> $query
     * @param string|null $term
     * @return Builder<CashBook>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('category', 'like', "%{$term}%")
                ->orWhere('note', 'like', "%{$term}%")
                ->orWhere('reference_no', 'like', "%{$term}%")
                ->orWhere('payment_method', 'like', "%{$term}%");
        });
    }

    /**
     * Scope by date range.
     *
     * @param Builder<CashBook> $query
     * @param string|null $from
     * @param string|null $to
     * @return Builder<CashBook>
     */
    public function scopeDateBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        if (! empty($from) && ! empty($to)) {
            return $query->whereBetween('date', [$from, $to]);
        }

        if (! empty($from)) {
            return $query->where('date', '>=', $from);
        }

        if (! empty($to)) {
            return $query->where('date', '<=', $to);
        }

        return $query;
    }
}
