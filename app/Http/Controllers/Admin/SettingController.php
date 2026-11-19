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
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'memo_subtitle' => 'nullable|string|max:500',
            'proprietor_name' => 'nullable|string|max:255',
            'proprietor_mobile' => 'nullable|string|max:100',
            'contact_numbers' => 'nullable|string|max:255',
            'operator_name' => 'nullable|string|max:255',
            'operator_contact' => 'nullable|string|max:100',
            'nursing_point_address' => 'nullable|string|max:500',
            'office_address' => 'nullable|string|max:500',
            'memo_slogan' => 'nullable|string|max:255',
            'memo_authority_signature' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['_token', 'site_logo', 'site_favicon', '_method']);

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
