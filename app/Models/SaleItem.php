<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sales_id',
        'sale_product_id',
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
        'sales_id' => 'integer',
        'sale_product_id' => 'integer',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Sales, SaleItem>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    /**
     * @return BelongsTo<SaleProduct, SaleItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(SaleProduct::class, 'sale_product_id');
    }

    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? '—';
    }
}
