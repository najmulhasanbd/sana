<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Display a listing of the settings.
     */
    public function index(): View
    {
        $settings = Setting::getAll();

        return view('admin.pages.setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $settings = Setting::getAll();

        return view('admin.pages.setting.index', compact('settings'));
    }

    /**
     * Store a newly created setting or batch of settings.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'site_favicon' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:1024',
            'business_name' => 'nullable|string|max:255',
            'memo_business_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:500',
            'memo_subtitle' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'proprietor_name' => 'nullable|string|max:255',
            'proprietor_mobile' => 'nullable|string|max:100',
            'contact_numbers' => 'nullable|string|max:255',
            'operator_name' => 'nullable|string|max:255',
            'operator_contact' => 'nullable|string|max:100',
            'nursing_point_address' => 'nullable|string|max:500',
            'office_address' => 'nullable|string|max:500',
            'memo_slogan' => 'nullable|string|max:255',
            'memo_authority_signature' => 'nullable|string|max:255',
            'box_preset_options' => 'nullable|string|max:1000',
            'poly_preset_options' => 'nullable|string|max:1000',
            'mir_preset_options' => 'nullable|string|max:1000',
            'default_poly_per_box' => 'nullable|numeric|min:0|max:100',
            'poly_per_box' => 'nullable|numeric|min:0|max:100',
            'default_qty_per_poly' => 'nullable|numeric|min:0',
            'mir_per_poly' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except(['_token', 'site_logo', 'site_favicon', '_method']);

        // Auto-synchronize box and poly calculations
        if ($request->filled('default_poly_per_box')) {
            $data['poly_per_box'] = $request->input('default_poly_per_box');
        } elseif ($request->filled('poly_per_box')) {
            $data['default_poly_per_box'] = $request->input('poly_per_box');
        }

        if ($request->filled('default_qty_per_poly')) {
            $data['mir_per_poly'] = $request->input('default_qty_per_poly');
        } elseif ($request->filled('mir_per_poly')) {
            $data['default_qty_per_poly'] = $request->input('mir_per_poly');
        }

        // Auto-synchronize unified business & memo fields to prevent duplicate/desynced data
        if ($request->filled('business_name')) {
            $data['memo_business_name'] = $request->input('business_name');
        } elseif ($request->filled('memo_business_name')) {
            $data['business_name'] = $request->input('memo_business_name');
        }

        if ($request->filled('memo_subtitle')) {
            $data['site_tagline'] = $request->input('memo_subtitle');
        } elseif ($request->filled('site_tagline')) {
            $data['memo_subtitle'] = $request->input('site_tagline');
        }

        if ($request->filled('proprietor_mobile')) {
            $data['mobile'] = $request->input('proprietor_mobile');
        } elseif ($request->filled('mobile')) {
            $data['proprietor_mobile'] = $request->input('mobile');
        }

        if ($request->filled('office_address')) {
            $data['address'] = $request->input('office_address');
        } elseif ($request->filled('address')) {
            $data['office_address'] = $request->input('address');
        }

        // Handle Site Logo upload (automatically converted to WebP via Intervention Image)
        if ($request->hasFile('site_logo')) {
            $path = $this->imageService->uploadWebp(
                file: $request->file('site_logo'),
                directory: 'uploads/settings',
                prefix: 'logo',
                maxWidth: 400,
                maxHeight: 120,
                oldPath: Setting::get('site_logo')
            );
            Setting::set('site_logo', $path);
        }

        // Handle Site Favicon upload (automatically converted to WebP via Intervention Image)
        if ($request->hasFile('site_favicon')) {
            $path = $this->imageService->uploadWebp(
                file: $request->file('site_favicon'),
                directory: 'uploads/settings',
                prefix: 'favicon',
                maxWidth: 128,
                maxHeight: 128,
                oldPath: Setting::get('site_favicon')
            );
            Setting::set('site_favicon', $path);
        }

        // Handle boolean switches like SMS enabled & Test mode
        $data['sms_enabled'] = $request->boolean('sms_enabled') ? '1' : '0';
        $data['sms_test_mode'] = $request->boolean('sms_test_mode') ? '1' : '0';

        // Save all text settings
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        toastr()->success('সকল সেটিংস সফলভাবে সংরক্ষণ করা হয়েছে!', 'সফল');

        return redirect()->route('setting.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id): RedirectResponse
    {
        return redirect()->route('setting.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|int $id): RedirectResponse
    {
        return redirect()->route('setting.index');
    }

    /**
     * Update the specified setting resource.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        return $this->store($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        $setting = Setting::find($id);
        if ($setting) {
            $setting->delete();
        }

        return redirect()->route('setting.index')->with('success', 'সেটিংস মুছে ফেলা হয়েছে!');
    }
}
