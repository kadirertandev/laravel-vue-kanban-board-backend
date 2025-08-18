<?php

use App\Models\User;

test('users can authenticate using the login screen', function () {
  $user = User::factory()->create();

  $response = $this->postJson('/api/login', [
    'email' => $user->email,
    'password' => 'password',
  ]);

  $this->assertAuthenticated();
  $response->assertStatus(201)
    ->assertJson([
      "status" => "success",
      "message" => "user logged in successfully",
    ]);
});

test('users can not authenticate with invalid password', function () {
  $user = User::factory()->create();

  $this->postJson('/api/login', [
    'email' => $user->email,
    'password' => 'wrong-password',
  ]);

  $this->assertGuest();
});

test('users can logout', function () {
  $user = User::factory()->create();

  $response = $this->actingAs($user)->postJson('/api/logout');

  $this->assertGuest();
  $response->assertStatus(204);
});
