<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    /**
     * Store a newly created employee advance payment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'employee_id.required' => 'কর্মচারী নির্বাচন করুন।',
            'employee_id.exists' => 'নির্বাচিত কর্মচারী সঠিক নয়।',
            'date.required' => 'অগ্রিমের তারিখ প্রদান করুন।',
            'amount.required' => 'অগ্রিমের টাকার পরিমাণ আবশ্যক।',
            'amount.min' => 'অগ্রিমের পরিমাণ ১ টাকার কম বা মাইনাস হতে পারবে না।',
            'payment_method.required' => 'পরিশোধের মাধ্যম নির্বাচন করুন।',
        ]);

        $validated['amount'] = max(1.0, (float) $validated['amount']);

        $advance = EmployeeAdvance::create($validated);
        $employee = Employee::find($validated['employee_id']);

        if (function_exists('toastr')) {
            $formattedAmount = number_format($advance->amount, 2);
            toastr()->success("কর্মচারী '{$employee->name}'-কে ৳{$formattedAmount} অগ্রিম প্রদান রেকর্ড করা হয়েছে!", 'অগ্রিম রেকর্ড সফল');
        }

        return back();
    }

    /**
     * Remove the specified advance record from storage.
     */
    public function destroy(EmployeeAdvance $advance): RedirectResponse
    {
        $employeeName = $advance->employee?->name ?? 'কর্মচারী';
        $amount = number_format($advance->amount, 2);

        $advance->delete();

        if (function_exists('toastr')) {
            toastr()->success("'{$employeeName}'-এর ৳{$amount} টাকার অগ্রিম রেকর্ডটি বাতিল করা হয়েছে!", 'সফল');
        }

        return back();
    }
}
