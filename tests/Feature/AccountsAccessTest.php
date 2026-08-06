<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_accounts_page(): void
    {
        $user = User::factory()->create([
            'username' => 'staff',
            'name' => 'Staff User',
            'email' => 'staff@example.com',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.accounts'));

        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_accounts_page(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.accounts'));

        $response->assertStatus(200);
    }
}
