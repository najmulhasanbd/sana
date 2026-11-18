<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invoice_no',
        'supplier_id',
        'purchase_date',
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
        'supplier_id' => 'integer',
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    /**
     * The supplier from whom the purchase was made.
     *
     * @return BelongsTo<Supplier, Purchase>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * The items included in this purchase.
     *
     * @return HasMany<PurchaseItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * The payments made for this purchase (initial & due payments).
     *
     * @return HasMany<PurchasePayment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class)->latest('payment_date')->latest('id');
    }

    /**
     * Generate sequential unique invoice number (e.g. PUR-00001).
     */
    public static function generateInvoiceNo(): string
    {
        $latest = self::orderByDesc('id')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        $candidate = 'PUR-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        while (self::where('invoice_no', $candidate)->exists()) {
            $nextNumber++;
            $candidate = 'PUR-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
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
     * Scope query by search term (invoice no, supplier name, supplier mobile).
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('invoice_no', 'like', "%{$term}%")
              ->orWhereHas('supplier', function (Builder $sq) use ($term): void {
                  $sq->where('name', 'like', "%{$term}%")
                     ->orWhere('mobile', 'like', "%{$term}%");
              });
        });
    }
}
