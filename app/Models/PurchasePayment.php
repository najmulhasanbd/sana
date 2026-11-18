<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasePayment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'purchase_id',
        'payment_date',
        'amount',
        'payment_method',
        'transaction_id',
        'note',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_id' => 'integer',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Purchase, PurchasePayment>
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
