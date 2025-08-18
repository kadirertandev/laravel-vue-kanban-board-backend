<?php

use App\Models\User;

it('creates a user', function () {
  $user = createUser();

  expect($user)->toBeInstanceOf(User::class);
  $this->assertDatabaseHas("users", [
    "id" => $user->id,
    "name" => $user->name,
    "email" => $user->email
  ]);
});
