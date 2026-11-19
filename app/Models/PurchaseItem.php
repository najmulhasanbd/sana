<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'purchase_id',
        'purchase_product_id',
        'box',
        'poly',
        'meer',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_id' => 'integer',
        'purchase_product_id' => 'integer',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Purchase, PurchaseItem>
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * @return BelongsTo<PurchaseProduct, PurchaseItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id');
    }
}
