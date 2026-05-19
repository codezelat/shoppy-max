<?php

namespace Tests\Feature;

use App\Models\Reseller;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_reseller_creation_creates_linked_login_account_with_copy_details(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('resellers.store'), $this->resellerPayload([
            'email' => 'regular.reseller@example.com',
            'return_fee' => 125,
        ]));

        $response->assertRedirect(route('resellers.index'));
        $response->assertSessionHas('created_login.email', 'regular.reseller@example.com');
        $response->assertSessionHas('created_login.login_url', route('login'));
        $response->assertSessionHas('created_login.role', 'reseller');
        $this->assertNotEmpty(session('created_login.password'));

        $reseller = Reseller::where('email', 'regular.reseller@example.com')->firstOrFail();
        $this->assertSame(Reseller::TYPE_RESELLER, $reseller->reseller_type);
        $this->assertNotNull($reseller->user_id);
        $this->assertSame($reseller->user_id, $reseller->userAccount->id);
        $this->assertTrue($reseller->userAccount->hasRole('reseller'));
        $this->assertFalse($reseller->userAccount->hasRole('direct reseller'));
        $this->assertSame('Regular Contact', $reseller->userAccount->name);
        $this->assertSame('0771234567', $reseller->userAccount->phone);
    }

    public function test_direct_reseller_creation_creates_linked_login_account_with_direct_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('direct-resellers.store'), $this->resellerPayload([
            'email' => 'direct.reseller@example.com',
        ]));

        $response->assertRedirect(route('direct-resellers.index'));
        $response->assertSessionHas('created_login.email', 'direct.reseller@example.com');
        $response->assertSessionHas('created_login.role', 'direct reseller');

        $reseller = Reseller::where('email', 'direct.reseller@example.com')->firstOrFail();
        $this->assertSame(Reseller::TYPE_DIRECT_RESELLER, $reseller->reseller_type);
        $this->assertNotNull($reseller->user_id);
        $this->assertTrue($reseller->userAccount->hasRole('direct reseller'));
        $this->assertFalse($reseller->userAccount->hasRole('reseller'));
    }

    public function test_generated_reseller_credentials_can_log_in(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('direct-resellers.store'), $this->resellerPayload([
            'email' => 'login.ready@example.com',
        ]));

        $password = session('created_login.password');
        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => 'login.ready@example.com',
            'password' => $password,
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs(User::where('email', 'login.ready@example.com')->first());
    }

    public function test_reseller_email_is_required_for_both_create_forms(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('resellers.store'), $this->resellerPayload(['email' => null]))
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)
            ->post(route('direct-resellers.store'), $this->resellerPayload(['email' => null]))
            ->assertSessionHasErrors('email');
    }

    public function test_reseller_email_must_not_reuse_another_user_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('resellers.store'), $this->resellerPayload(['email' => 'taken@example.com']))
            ->assertSessionHasErrors('email');
    }

    public function test_reseller_update_syncs_existing_login_account_without_resetting_password(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('direct-resellers.store'), $this->resellerPayload([
            'email' => 'sync.before@example.com',
        ]));

        $reseller = Reseller::where('email', 'sync.before@example.com')->firstOrFail();
        $userId = $reseller->user_id;
        $passwordHash = $reseller->userAccount->password;

        $this->actingAs($admin)
            ->put(route('direct-resellers.update', $reseller), $this->resellerPayload([
                'name' => 'Updated Direct Contact',
                'email' => 'sync.after@example.com',
                'mobile' => '0777654321',
            ]))
            ->assertRedirect(route('direct-resellers.index'))
            ->assertSessionMissing('created_login');

        $reseller->refresh();
        $this->assertSame($userId, $reseller->user_id);
        $this->assertSame('sync.after@example.com', $reseller->userAccount->email);
        $this->assertSame('Updated Direct Contact', $reseller->userAccount->name);
        $this->assertSame('0777654321', $reseller->userAccount->phone);
        $this->assertSame($passwordHash, $reseller->userAccount->password);
    }

    public function test_reseller_account_dashboard_shows_linked_reseller_details(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('resellers.store'), $this->resellerPayload([
            'business_name' => 'Dashboard Reseller Business',
            'email' => 'dashboard.reseller@example.com',
            'due_amount' => 750,
            'return_fee' => 50,
        ]));

        $reseller = Reseller::where('email', 'dashboard.reseller@example.com')->firstOrFail();

        $response = $this->actingAs($reseller->userAccount)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Reseller Business');
        $response->assertSee('Reseller Account');
        $response->assertSee('Rs. 750.00');
        $response->assertSee('Return Fee');
        $response->assertSee('Rs. 50.00');
    }

    public function test_deleting_reseller_removes_dedicated_login_account(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('direct-resellers.store'), $this->resellerPayload([
            'email' => 'delete.login@example.com',
        ]));

        $reseller = Reseller::where('email', 'delete.login@example.com')->firstOrFail();
        $userId = $reseller->user_id;

        $this->actingAs($admin)
            ->delete(route('direct-resellers.destroy', $reseller))
            ->assertRedirect(route('direct-resellers.index'));

        $this->assertDatabaseMissing('resellers', ['id' => $reseller->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    private function resellerPayload(array $overrides = []): array
    {
        return array_merge([
            'business_name' => 'Regular Business',
            'name' => 'Regular Contact',
            'email' => 'reseller@example.com',
            'mobile' => '0771234567',
            'landline' => null,
            'address' => '123 Main Street',
            'country' => 'Sri Lanka',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo',
            'due_amount' => 0,
            'return_fee' => 0,
            'couriers' => [],
        ], $overrides);
    }
}
