<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Show the admin password change form.
     */
    public function edit(): View
    {
        return view('admin.pages.auth.password-change');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if (function_exists('toastr')) {
            toastr()->success('পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে!', 'সফল');
        }

        return back()->with('status', 'password-updated');
    }
}
