<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        return view('admin.index');
    }

    /**
     * Clear application cache using Artisan commands.
     */
    public function clearCache(): RedirectResponse
    {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        if (function_exists('toastr')) {
            toastr()->success('সিস্টেম ক্যাশ (Views, Routes, Config & Cache) সফলভাবে পরিষ্কার করা হয়েছে!', 'ক্যাশ ক্লিয়ার');
        }

        return back();
    }
}
