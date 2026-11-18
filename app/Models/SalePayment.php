<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sales_id',
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
        'sales_id' => 'integer',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Sales, SalePayment>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }
}
