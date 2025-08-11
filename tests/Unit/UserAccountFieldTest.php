<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAccountFieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_account_field()
    {
        $user = User::create([
            'account' => 'testuser',
            'full_name' => 'Test User',
            'display_name' => null,
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertEquals('testuser', $user->account);
        $this->assertDatabaseHas('users', [
            'account' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_account_field_must_be_unique()
    {
        User::create([
            'account' => 'testuser',
            'full_name' => 'Test User 1',
            'display_name' => null,
            'email' => 'test1@example.com',
            'password' => 'password123',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'account' => 'testuser',
            'full_name' => 'Test User 2',
            'display_name' => null,
            'email' => 'test2@example.com',
            'password' => 'password123',
        ]);
    }

    public function test_display_name_returns_display_name_when_set()
    {
        $user = new User([
            'account' => 'testuser',
            'full_name' => 'Test User',
            'display_name' => 'Display Name',
        ]);

        $this->assertEquals('Display Name', $user->visible_name);
    }

    public function test_display_name_returns_full_name_when_display_name_is_empty()
    {
        $user = new User([
            'account' => 'testuser',
            'full_name' => 'Test User',
            'display_name' => null,
        ]);

        $this->assertEquals('Test User', $user->visible_name);
    }

    public function test_display_name_returns_account_when_both_display_name_and_full_name_are_empty()
    {
        $user = new User([
            'account' => 'testuser',
            'full_name' => null,
            'display_name' => null,
        ]);

        $this->assertEquals('testuser', $user->visible_name);
    }
}
