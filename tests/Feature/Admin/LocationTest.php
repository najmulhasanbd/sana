<?php

namespace Tests\Feature\Admin;

use App\Models\District;
use App\Models\Division;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                              DIVISION TESTS                                */
    /* -------------------------------------------------------------------------- */

    public function test_guest_cannot_access_divisions(): void
    {
        $response = $this->get(route('admin.division.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_division_index(): void
    {
        Division::create(['name' => 'Dhaka', 'status' => true]);

        $response = $this->actingAs($this->user)->get(route('admin.division.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.division.index');
        $response->assertSee('Dhaka');
    }

    public function test_can_create_division(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.division.store'), [
            'name' => 'Sylhet',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.division.index'));
        $this->assertDatabaseHas('divisions', [
            'name' => 'Sylhet',
            'status' => true,
        ]);
    }

    public function test_can_update_division(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);

        $response = $this->actingAs($this->user)->put(route('admin.division.update', $division), [
            'name' => 'Dhaka Division',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.division.index'));
        $this->assertDatabaseHas('divisions', [
            'id' => $division->id,
            'name' => 'Dhaka Division',
        ]);
    }

    public function test_can_toggle_division_status(): void
    {
        $division = Division::create(['name' => 'Khulna', 'status' => true]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('admin.division.toggle-status', $division));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => false,
        ]);
        $this->assertDatabaseHas('divisions', [
            'id' => $division->id,
            'status' => false,
        ]);
    }

    public function test_can_delete_division(): void
    {
        $division = Division::create(['name' => 'Barishal', 'status' => true]);

        $response = $this->actingAs($this->user)->delete(route('admin.division.destroy', $division));

        $response->assertRedirect(route('admin.division.index'));
        $this->assertDatabaseMissing('divisions', ['id' => $division->id]);
    }

    /* -------------------------------------------------------------------------- */
    /*                              DISTRICT TESTS                                */
    /* -------------------------------------------------------------------------- */

    public function test_authenticated_user_can_view_district_index(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        District::create(['division_id' => $division->id, 'name' => 'Gazipur', 'status' => true]);

        $response = $this->actingAs($this->user)->get(route('admin.district.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.district.index');
        $response->assertSee('Gazipur');
    }

    public function test_can_create_district(): void
    {
        $division = Division::create(['name' => 'Mymensingh', 'status' => true]);

        $response = $this->actingAs($this->user)->post(route('admin.district.store'), [
            'division_id' => $division->id,
            'name' => 'Jamalpur',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.district.index'));
        $this->assertDatabaseHas('districts', [
            'division_id' => $division->id,
            'name' => 'Jamalpur',
        ]);
    }

    public function test_can_update_district(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Narayanganj', 'status' => true]);

        $response = $this->actingAs($this->user)->put(route('admin.district.update', $district), [
            'division_id' => $division->id,
            'name' => 'Narayanganj City',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.district.index'));
        $this->assertDatabaseHas('districts', [
            'id' => $district->id,
            'name' => 'Narayanganj City',
        ]);
    }

    public function test_can_delete_district(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Manikganj', 'status' => true]);

        $response = $this->actingAs($this->user)->delete(route('admin.district.destroy', $district));

        $response->assertRedirect(route('admin.district.index'));
        $this->assertDatabaseMissing('districts', ['id' => $district->id]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                ZONE TESTS                                  */
    /* -------------------------------------------------------------------------- */

    public function test_authenticated_user_can_view_zone_index(): void
    {
        $division = Division::create(['name' => 'Mymensingh', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Mymensingh', 'status' => true]);
        Zone::create(['district_id' => $district->id, 'name' => 'Trishal', 'status' => true]);

        $response = $this->actingAs($this->user)->get(route('admin.zone.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pages.zone.index');
        $response->assertSee('Trishal');
    }

    public function test_can_create_zone(): void
    {
        $division = Division::create(['name' => 'Mymensingh', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Mymensingh', 'status' => true]);

        $response = $this->actingAs($this->user)->post(route('admin.zone.store'), [
            'district_id' => $district->id,
            'name' => 'Bhaluka',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.zone.index'));
        $this->assertDatabaseHas('zones', [
            'district_id' => $district->id,
            'name' => 'Bhaluka',
        ]);
    }

    public function test_can_update_zone(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Dhaka', 'status' => true]);
        $zone = Zone::create(['district_id' => $district->id, 'name' => 'Mirpur-10', 'status' => true]);

        $response = $this->actingAs($this->user)->put(route('admin.zone.update', $zone), [
            'district_id' => $district->id,
            'name' => 'Mirpur-1',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.zone.index'));
        $this->assertDatabaseHas('zones', [
            'id' => $zone->id,
            'name' => 'Mirpur-1',
        ]);
    }

    public function test_can_delete_zone(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Dhaka', 'status' => true]);
        $zone = Zone::create(['district_id' => $district->id, 'name' => 'Uttara', 'status' => true]);

        $response = $this->actingAs($this->user)->delete(route('admin.zone.destroy', $zone));

        $response->assertRedirect(route('admin.zone.index'));
        $this->assertDatabaseMissing('zones', ['id' => $zone->id]);
    }

    public function test_location_api_endpoints(): void
    {
        $division = Division::create(['name' => 'Dhaka', 'status' => true]);
        $district = District::create(['division_id' => $division->id, 'name' => 'Gazipur', 'status' => true]);
        $zone = Zone::create(['district_id' => $district->id, 'name' => 'Tongi', 'status' => true]);

        $response1 = $this->actingAs($this->user)->getJson(route('admin.locations.districts', $division->id));
        $response1->assertStatus(200);
        $response1->assertJsonFragment(['name' => 'Gazipur']);

        $response2 = $this->actingAs($this->user)->getJson(route('admin.locations.zones', $district->id));
        $response2->assertStatus(200);
        $response2->assertJsonFragment(['name' => 'Tongi']);
    }
}
