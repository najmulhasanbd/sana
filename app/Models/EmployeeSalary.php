<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'salary_month',
        'payment_date',
        'base_salary',
        'bonus_amount',
        'advance_deduction',
        'net_salary',
        'payment_method',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'date',
        'base_salary' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    /**
     * Relationship with employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Human readable month name with Bengali representation.
     */
    public function getSalaryMonthNameAttribute(): string
    {
        if (! $this->salary_month) {
            return '—';
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $this->salary_month);
            $enMonth = $date->format('F Y');

            $bnMonths = [
                'January' => 'জানুয়ারি',
                'February' => 'ফেব্রুয়ারি',
                'March' => 'মার্চ',
                'April' => 'এপ্রিল',
                'May' => 'মে',
                'June' => 'জুন',
                'July' => 'জুলাই',
                'August' => 'আগস্ট',
                'September' => 'সেপ্টেম্বর',
                'October' => 'অক্টোবর',
                'November' => 'নভেম্বর',
                'December' => 'ডিসেম্বর',
            ];

            $bnMonthName = $bnMonths[$date->format('F')] ?? $date->format('F');
            $year = $date->format('Y');

            return "{$bnMonthName} {$year} ({$enMonth})";
        } catch (\Throwable $e) {
            return $this->salary_month;
        }
    }
}
