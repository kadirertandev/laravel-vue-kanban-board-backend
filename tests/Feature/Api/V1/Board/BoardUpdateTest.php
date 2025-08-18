<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->apiPrefix = "/api/v1";
});

it('denies unauthenticated users to update any board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->putJson("{$this->apiPrefix}/boards/{$board->id}", dummyBoardData(1));

  $response->assertStatus(401);
});

it('denies users to update boards they do not own', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user2)->putJson("{$this->apiPrefix}/boards/{$board->id}", dummyBoardData(1));

  $response->assertStatus(403);
});

it('allows users to update boards they do own', function () {
  $board = createBoardForUser($this->user, 1);
  $updateData = [
    "title" => "Board 1 is now updated",
    "description" => "Board 1 is now updated (description)",
  ];

  $response = $this->actingAs($this->user)->putJson("{$this->apiPrefix}/boards/{$board->id}", $updateData);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $board->id,
      "title" => $updateData["title"],
      "description" => $updateData["description"],
      "createdAtFrontend" => $board->created_at->diffForHumans(),
      "createdAt" => $board->created_at
    ]);

  $this->assertDatabaseHas("boards", [
    "user_id" => $this->user->id,
    ...$updateData
  ]);
});

it('returns expected json structure on success', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->putJson("{$this->apiPrefix}/boards/{$board->id}", dummyBoardData(1));

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "title",
        "description",
        "createdAtFrontend",
        "createdAt",
      ]
    ]);
});

it('returns error when fields are invalid', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->putJson("{$this->apiPrefix}/boards/{$board->id}", []);

  $response->assertStatus(422)
    ->assertInvalid([
      "title" => "The title field is required.",
      "description" => "The description field is required.",
    ])
    ->assertJsonStructure([
      "message",
      "errors" => [
        "title" => [],
        "description" => []
      ]
    ]);
});

