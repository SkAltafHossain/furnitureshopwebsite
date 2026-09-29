<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_email_only()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_user_can_register_with_phone_only()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Doe',
            'phone' => '+1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_register_with_both_email_and_phone()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '+1987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'bob@example.com',
            'phone' => '+1987654321',
        ]);
    }

    public function test_registration_requires_both_email_and_phone()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'phone']);
    }

    public function test_email_must_be_unique()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'phone' => '+1111111111',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'phone' => '+2222222222',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_phone_must_be_unique()
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_user_can_login_with_email()
    {
        $user = User::factory()->withEmailOnly()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_user_can_login_with_phone()
    {
        $user = User::factory()->withPhoneOnly()->create([
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => '+1234567890',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_login_requires_email_or_phone()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        User::factory()->withEmailOnly()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_login_with_phone_fails_with_invalid_credentials()
    {
        User::factory()->withPhoneOnly()->create([
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => '+1234567890',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_user_can_update_profile_with_email()
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => 'Updated Name',
            'email' => 'new@example.com',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_user_can_update_profile_with_phone()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
            'phone' => '+1987654321',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '+1987654321',
        ]);
    }

    public function test_user_update_requires_both_email_and_phone()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'phone']);
    }

    public function test_user_update_email_must_be_unique()
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user1)->putJson('/api/user', [
            'name' => 'Updated Name',
            'email' => 'user2@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_update_phone_must_be_unique()
    {
        $user1 = User::factory()->create([
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::factory()->create([
            'phone' => '+1987654321',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user1)->putJson('/api/user', [
            'name' => 'Updated Name',
            'phone' => '+1987654321',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_user_can_keep_own_email_when_updating()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
    }

    public function test_user_can_keep_own_phone_when_updating()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/user', [
            'name' => 'Updated Name',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
    }
}
