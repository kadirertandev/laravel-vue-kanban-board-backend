<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}";
});

it('denies unauthenticated users to delete any column', function () {
  $response = $this->deleteJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies users to delete columns they do not own', function () {
  $response = $this->actingAs($this->user2)->deleteJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows users to delete columns they do own', function () {
  $response = $this->actingAs($this->user)->deleteJson($this->endPoint);

  $response->assertStatus(204)
    ->assertNoContent();

  $this->assertDatabaseMissing("columns", [
    "board_id" => $this->board->id,
    "title" => $this->column->title,
    "description" => $this->column->description,
  ]);
});
