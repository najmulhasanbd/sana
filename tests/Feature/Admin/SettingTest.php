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

test('user can change password and is redirected to login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);

    $response = $this->actingAs($user)->put(route('password.change.update'), [
        'current_password' => 'old-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('admin can clear system cache via artisan route', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.cache.clear'));

    $response->assertRedirect();
    $response->assertSessionHas('custom_toastr_messages');
});
