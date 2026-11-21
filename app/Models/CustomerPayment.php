<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPayment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'voucher_no',
        'customer_id',
        'payment_date',
        'amount',
        'previous_due',
        'remaining_due',
        'payment_method',
        'transaction_id',
        'note',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'previous_due' => 'decimal:2',
        'remaining_due' => 'decimal:2',
        'created_by' => 'integer',
    ];

    /**
     * @return BelongsTo<Customer, CustomerPayment>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<SalePayment>
     */
    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'customer_payment_id');
    }

    /**
     * @return BelongsTo<User, CustomerPayment>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate sequential unique money receipt voucher number (e.g. MR-00001).
     */
    public static function generateVoucherNo(): string
    {
        $latest = self::orderByDesc('id')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        $candidate = 'MR-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        while (self::where('voucher_no', $candidate)->exists()) {
            $nextNumber++;
            $candidate = 'MR-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Scope query by search term.
     *
     * @param Builder<CustomerPayment> $query
     * @return Builder<CustomerPayment>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('voucher_no', 'like', "%{$term}%")
              ->orWhere('transaction_id', 'like', "%{$term}%")
              ->orWhere('note', 'like', "%{$term}%")
              ->orWhereHas('customer', function (Builder $cq) use ($term): void {
                  $cq->where('name', 'like', "%{$term}%")
                     ->orWhere('mobile', 'like', "%{$term}%");
              });
        });
    }
}
