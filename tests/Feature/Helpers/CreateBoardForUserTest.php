<?php

beforeEach(function () {
  $this->user = createUser();
});

it('throws exception when amount is zero', function () {
  createBoardForUser($this->user, 0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  createBoardForUser($this->user, -4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('creates 1 board for given user when amount is 1', function () {
  $board = createBoardForUser($this->user, 1);

  expect($this->user->boards()->count())->toBe(1);
  $this->assertDatabaseHas("boards", [
    "user_id" => $this->user->id,
    "title" => "Board 1",
    "description" => "Board 1 Description"
  ]);
});

it('creates multiple boards for given user when amount is bigger than 1', function () {
  createBoardForUser($this->user, 3);
  $boardsArray = [
    ["user_id" => $this->user->id, "title" => "Board 1", "description" => "Board 1 Description"],
    ["user_id" => $this->user->id, "title" => "Board 2", "description" => "Board 2 Description"],
    ["user_id" => $this->user->id, "title" => "Board 3", "description" => "Board 3 Description"]
  ];

  expect($this->user->boards()->count())->toBe(3);
  foreach ($boardsArray as $data) {
    $this->assertDatabaseHas(
      "boards",
      $data
    );
  }
});
