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

it('denies access to the tasks show endpoint for unauthenticated users', function () {
  $response = $this->getJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies access to the tasks show endpoint when user does not own the task', function () {
  $response = $this->actingAs($this->user2)->getJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows access to the tasks show endpoint when user does own the task', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertExactJson([
      "data" => [
        "id" => $this->task->id,
        "description" => $this->task->description,
        "position" => $this->task->position,
        "createdAt" => $this->task->created_at->diffForHumans()
      ]
    ]);
});

it('returns expected json structure for the task', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "description",
        "position",
        "createdAt"
      ]
    ])
    ->assertJsonMissingPath("data.relations");
});