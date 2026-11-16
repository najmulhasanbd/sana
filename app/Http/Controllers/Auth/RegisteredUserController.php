<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view (redirects to login).
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login');
    }

    /**
     * Handle an incoming registration request (registration disabled, redirects to login).
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('login');
    }
}
