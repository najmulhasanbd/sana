<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sales extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'sale_date',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'note',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Customer, Sales>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<SaleItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sales_id');
    }

    /**
     * @return HasMany<SalePayment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class, 'sales_id')->latest('payment_date')->latest('id');
    }

    /**
     * Generate sequential unique invoice number (e.g. SALE-00001).
     */
    public static function generateInvoiceNo(): string
    {
        $latest = self::orderByDesc('id')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        $candidate = 'SALE-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        while (self::where('invoice_no', $candidate)->exists()) {
            $nextNumber++;
            $candidate = 'SALE-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Alias accessor for invoice_number -> invoice_no
     */
    public function getInvoiceNumberAttribute(): ?string
    {
        return $this->invoice_no;
    }

    /**
     * Recalculate paid amount, due amount and update payment status.
     */
    public function recalculatePayments(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount');
        $totalAmount = (float) $this->total_amount;
        $dueAmount = max(0, $totalAmount - $totalPaid);

        if ($totalPaid >= $totalAmount) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'due';
        }

        $this->update([
            'paid_amount' => $totalPaid,
            'due_amount' => $dueAmount,
            'payment_status' => $status,
        ]);
    }

    /**
     * Scope query by search term (invoice no, customer name, customer mobile).
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('invoice_no', 'like', "%{$term}%")
              ->orWhereHas('customer', function (Builder $sq) use ($term): void {
                  $sq->where('name', 'like', "%{$term}%")
                     ->orWhere('mobile', 'like', "%{$term}%");
              });
        });
    }
}
