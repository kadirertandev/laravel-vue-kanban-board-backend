<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard($this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}";
});

it('denies unauthenticated users to update any column', function () {
  $response = $this->putJson($this->endPoint, dummyColumnData(1));

  $response->assertStatus(401);
});

it('denies users to update columns they do not own', function () {
  $response = $this->actingAs($this->user2)->putJson($this->endPoint, dummyBoardData(1));

  $response->assertStatus(403);
});

it('allows users to update columns they do own', function () {
  $updateData = [
    "title" => "Column 1 is now updated",
    "description" => "Column 1 is now updated (description)",
  ];

  $response = $this->actingAs($this->user)->putJson($this->endPoint, $updateData);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->column->id,
      "title" => $updateData["title"],
      "description" => $updateData["description"],
      "position" => $this->column->position,
      "createdAt" => $this->column->created_at->diffForHumans()
    ]);

  $this->assertDatabaseHas("columns", [
    "board_id" => $this->board->id,
    ...$updateData
  ]);
});

it('returns expected json structure on success', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, dummyBoardData(1));

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "title",
        "description",
        "position",
        "createdAt",
        "relations" => [
          "board_id"
        ]
      ]
    ]);
});

it('returns error when fields are invalid', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, []);

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