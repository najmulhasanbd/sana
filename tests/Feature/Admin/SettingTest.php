<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can view settings page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('setting.index'));

    $response->assertOk();
    $response->assertSee('ব্যবসা ও সিস্টেম সেটিংস');
    $response->assertSee('btn-submit-with-spinner');
    $response->assertSee('image-dimension-help');
});

test('user can save settings and image is converted to webp', function () {
    $user = User::factory()->create();

    $dummyImage = UploadedFile::fake()->image('mylogo.png', 300, 100);

    $response = $this->actingAs($user)->post(route('setting.store'), [
        'business_name' => 'সানা ফিশারিজ ও হ্যাচারি',
        'site_logo' => $dummyImage,
        'mobile' => '01711000000',
    ]);

    $response->assertRedirect(route('setting.index'));
    $response->assertSessionHas('custom_toastr_messages');

    expect(Setting::get('business_name'))->toBe('সানা ফিশারিজ ও হ্যাচারি');

    $logoPath = Setting::get('site_logo');
    expect($logoPath)->not->toBeNull();
    expect($logoPath)->toEndWith('.webp');
    expect(file_exists(public_path($logoPath)))->toBeTrue();

    // Cleanup generated file
    @unlink(public_path($logoPath));
});

test('authenticated user can view password change page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.change'));

    $response->assertOk();
    $response->assertSee('পাসওয়ার্ড পরিবর্তন');
    $response->assertSee('btn-submit-with-spinner');
});
