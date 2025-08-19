<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}/move";
});

it('denies unauthenticated users to move any column', function () {
  $response = $this->putJson($this->endPoint, [
    "position" => 1000
  ]);

  $response->assertStatus(401);
});

it('denies users to move columns they do not own', function () {
  $response = $this->actingAs($this->user2)->putJson($this->endPoint, [
    "position" => 1000
  ]);

  $response->assertStatus(403);
});

it('allows users to move columns they do own', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "position" => 1000
  ]);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->column->id,
      "title" => $this->column->title,
      "description" => $this->column->description,
      "position" => 1000,
      "createdAt" => $this->column->created_at->diffForHumans()
    ]);

  $this->assertDatabaseHas("columns", [
    "id" => $this->column->id,
    "board_id" => $this->board->id,
    "position" => 1000
  ]);
});

it('returns expected json structure on success', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "position" => 1000
  ]);

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "title",
        "description",
        "position",
        "createdAt"
      ]
    ])->assertJsonMissingPath("data.relations");
});

it('returns error when fields are invalid', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint);

  $response->assertStatus(422)
    ->assertInvalid([
      "position" => "The position field is required.",
    ])
    ->assertJsonStructure([
      "message",
      "errors" => [
        "position" => []
      ]
    ]);
});

it('rounds column position to 5 decimal places when moving', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "position" => 1.123456
  ]);
  $response->assertStatus(200);
  $this->assertDatabaseHas("columns", [
    "id" => $this->column->id,
    "board_id" => $this->board->id,
    "position" => 1.12346 //passes because round(1.123456, 5) equals 1.12346
  ]);

  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "position" => 1.123454
  ]);
  $response->assertStatus(200);
  $this->assertDatabaseHas("columns", [
    "id" => $this->column->id,
    "board_id" => $this->board->id,
    "position" => 1.12345 //passes because round(1.123454, 5) equals 1.12345
  ]);
});

it('repositions columns when position of a column goes below minimum', function () {
  $this->board = createBoardForUser($this->user);
  $columns = createColumnForBoard($this->board, 2);

  $response = $this->actingAs($this->user)->putJson("{$this->apiPrefix}/columns/{$columns[1]->id}/move", [
    "position" => 0.00001
  ]);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $columns[1]->id,
      "title" => $columns[1]->title,
      "description" => $columns[1]->description,
      "position" => 60000,
      "createdAt" => $columns[1]->created_at->diffForHumans()
    ]);

  $this->assertDatabaseHas("columns", [
    "id" => $columns[1]->id,
    "board_id" => $this->board->id,
    "position" => 60000
  ]);

  $this->assertDatabaseHas("columns", [
    "id" => $columns[0]->id,
    "board_id" => $this->board->id,
    "position" => 120000
  ]);
});
