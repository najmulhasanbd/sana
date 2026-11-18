<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'designation_id',
        'designation',
        'mobile',
        'nid',
        'address',
        'joining_date',
        'monthly_salary',
        'status',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'monthly_salary' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Relationship with designation.
     */
    public function designationRef(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Designation display name.
     */
    public function getDesignationNameAttribute(): string
    {
        return $this->designationRef?->name ?? $this->designation ?? '—';
    }

    /**
     * Relationship with employee advances.
     */
    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class)->latest('date');
    }

    /**
     * Relationship with employee salary payments.
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(EmployeeSalary::class)->latest('payment_date');
    }

    /**
     * Total advance taken by the employee.
     */
    public function getTotalAdvanceAttribute(): float
    {
        return (float) $this->advances()->sum('amount');
    }

    /**
     * Total advance deducted from salaries.
     */
    public function getTotalAdvanceDeductedAttribute(): float
    {
        return (float) $this->salaries()->sum('advance_deduction');
    }

    /**
     * Current outstanding advance balance.
     * Guaranteed to be zero or positive (never negative).
     */
    public function getAdvanceBalanceAttribute(): float
    {
        return max(0.0, (float) ($this->total_advance - $this->total_advance_deducted));
    }

    /**
     * Total net salary paid to the employee.
     */
    public function getTotalSalaryPaidAttribute(): float
    {
        return (float) $this->salaries()->sum('net_salary');
    }

    /**
     * Scope to filter active employees.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope to search employees by name, mobile, or designation.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('mobile', 'like', "%{$term}%")
                ->orWhere('designation', 'like', "%{$term}%")
                ->orWhere('nid', 'like', "%{$term}%")
                ->orWhereHas('designationRef', function (Builder $dq) use ($term): void {
                    $dq->where('name', 'like', "%{$term}%");
                });
        });
    }
}
