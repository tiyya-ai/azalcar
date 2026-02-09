<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user cannot edit listing they don't own
     */
    public function test_user_cannot_edit_others_listing()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        
        $listing = Listing::factory()->create(['user_id' => $owner->id]);
        
        $this->actingAs($otherUser);
        
        $response = $this->get(route('listings.frontend.edit', $listing->slug));
        
        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test admin can edit any listing
     */
    public function test_admin_can_edit_any_listing()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        
        $listing = Listing::factory()->create(['user_id' => $owner->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get(route('listings.frontend.edit', $listing->slug));
        
        $response->assertStatus(200); // Success
    }

    /**
     * Test non-admin cannot access admin panel
     */
    public function test_non_admin_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user);

        $response = $this->get('/admin/');

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test cannot delete last admin
     */
    public function test_cannot_delete_last_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->delete('/admin/users/' . $admin->id);

        $response->assertSessionHas('error', 'Cannot delete the last admin.');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Test regular user cannot change their role
     */
    public function test_user_cannot_change_own_role()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $this->actingAs($user);
        
        // Attempt mass assignment attack
        $user->fill(['role' => 'admin']);
        
        // Role should be guarded
        $this->assertEquals('user', $user->role);
    }

    /**
     * Test user cannot directly modify balance
     */
    public function test_user_cannot_directly_modify_balance()
    {
        $user = User::factory()->create(['balance' => 1000]);
        
        // Attempt mass assignment attack
        $user->fill(['balance' => 999999]);
        
        // Balance should be guarded
        $this->assertEquals(1000, $user->balance);
    }
}
