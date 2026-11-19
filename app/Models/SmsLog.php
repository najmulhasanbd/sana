<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'sales_id',
        'recipient',
        'message',
        'type',
        'status',
        'gateway',
        'response',
        'sent_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'sales_id' => 'integer',
        'sent_by' => 'integer',
    ];

    /**
     * The customer who received the SMS.
     *
     * @return BelongsTo<Customer, SmsLog>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The sales invoice related to the SMS.
     *
     * @return BelongsTo<Sales, SmsLog>
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    /**
     * The admin user who initiated the SMS.
     *
     * @return BelongsTo<User, SmsLog>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope query by search term.
     *
     * @param  Builder<SmsLog>  $query
     * @param  string|null  $term
     * @return Builder<SmsLog>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('recipient', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%")
                ->orWhereHas('customer', function (Builder $custQuery) use ($term): void {
                    $custQuery->where('name', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Scope query by SMS type.
     *
     * @param  Builder<SmsLog>  $query
     * @param  string|null  $type
     * @return Builder<SmsLog>
     */
    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Scope query by SMS status.
     *
     * @param  Builder<SmsLog>  $query
     * @param  string|null  $status
     * @return Builder<SmsLog>
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
