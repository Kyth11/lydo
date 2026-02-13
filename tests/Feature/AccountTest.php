<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_does_not_see_barangay_field_on_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('account.edit'));

        $response->assertStatus(200);
        $response->assertDontSee('name="barangay"');
        $response->assertDontSee('Select Barangay');
    }

    public function test_sk_sees_barangay_field_on_edit_page(): void
    {
        $sk = User::factory()->create(['role' => 'sk', 'barangay' => 'Awang']);

        $response = $this->actingAs($sk)->get(route('account.edit'));

        $response->assertStatus(200);
        $response->assertSee('name="barangay"');
        $response->assertSee('Select Barangay');
        $response->assertSee('Awang');
    }

    public function test_admin_cannot_change_barangay(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'barangay' => null]);

        $response = $this->actingAs($admin)->patch(route('account.update'), [
            'name' => 'Admin Updated',
            'email' => $admin->email,
            'barangay' => 'Poblacion',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Updated',
            'barangay' => null,
        ]);
    }

    public function test_sk_can_update_barangay(): void
    {
        $sk = User::factory()->create(['role' => 'sk', 'barangay' => 'Awang']);

        $response = $this->actingAs($sk)->patch(route('account.update'), [
            'name' => 'SK Updated',
            'email' => $sk->email,
            'barangay' => 'Poblacion',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $sk->id,
            'name' => 'SK Updated',
            'barangay' => 'Poblacion',
        ]);
    }
}
