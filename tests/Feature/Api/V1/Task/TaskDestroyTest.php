<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);
  $this->task = createTaskForColumn(column: $this->column);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/tasks/{$this->task->id}";
});

it('denies unauthenticated users to delete any task', function () {
  $response = $this->deleteJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies users to delete tasks they do not own', function () {
  $response = $this->actingAs($this->user2)->deleteJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows users to delete tasks they do own', function () {
  $response = $this->actingAs($this->user)->deleteJson($this->endPoint);

  $response->assertStatus(204)
    ->assertNoContent();

  $this->assertDatabaseMissing("tasks", [
    "column_id" => $this->column->id,
    "description" => $this->task->description,
  ]);
});
