<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}/tasks";
});

it('denies access to the tasks index endpoint for unauthenticated users', function () {
  $response = $this->getJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies access to the tasks index endpoint when user does not own the tasks', function () {
  createTaskForColumn($this->column, 3);
  $response = $this->actingAs($this->user2)->getJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows access to the tasks index endpoint when user does own the tasks', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200);
});

it('returns empty data', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertExactJson(["data" => []])
    ->assertJsonCount(0, "data");
});

it('returns the tasks belongs to authenticated user', function () {
  $tasks = createTaskForColumn($this->column, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "description" => $tasks[0]->description
    ])
    ->assertJsonCount(3, "data");
});

it('returns expected json structure for single task', function () {
  $task = createTaskForColumn($this->column, 1);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(1, "data")
    ->assertJsonFragment([
      "description" => $task->description
    ])
    ->assertJsonStructure([
      "data" => [
        [
          "id",
          "description",
          "position",
          "createdAt",
        ]
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $task->id,
          "description" => $task->description,
          "position" => $task->position,
          "createdAt" => $task->created_at->diffForHumans(),
        ]
      ]
    ]);
});

it('returns expected json structure for multiple tasks', function () {
  $tasks = createTaskForColumn($this->column, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(3, "data")
    ->assertJsonFragment([
      "description" => $tasks[0]->description
    ])
    ->assertJsonStructure([
      "data" => [
        "*" => [
          "id",
          "description",
          "position",
          "createdAt",
        ]
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $tasks[0]->id,
          "description" => $tasks[0]->description,
          "position" => $tasks[0]->position,
          "createdAt" => $tasks[0]->created_at->diffForHumans(),
        ],
        [
          "id" => $tasks[1]->id,
          "description" => $tasks[1]->description,
          "position" => $tasks[1]->position,
          "createdAt" => $tasks[1]->created_at->diffForHumans(),
        ],
        [
          "id" => $tasks[2]->id,
          "description" => $tasks[2]->description,
          "position" => $tasks[2]->position,
          "createdAt" => $tasks[2]->created_at->diffForHumans(),
        ]
      ]
    ]);
});

it('returns expected json structure when relationships are requested', function () {
  $tasks = createTaskForColumn($this->column, 2);

  $route = route("columns.tasks.index", [
    "column" => $this->column,
    "withTaskColumn" => true
  ]);
  $response = $this->actingAs($this->user)->getJson($route);

  $response->assertStatus(200)
    ->assertJsonCount(2, "data")
    ->assertJsonFragment([
      "description" => $tasks[0]->description,
      "position" => $tasks[0]->position,
    ])
    ->assertJsonStructure([
      "data" => [
        "*" => [
          "id",
          "description",
          "position",
          "createdAt",
          "relations" => [
            "column_id"
          ]
        ]
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $tasks[0]->id,
          "description" => $tasks[0]->description,
          "position" => $tasks[0]->position,
          "createdAt" => $tasks[0]->created_at->diffForHumans(),
          "relations" => [
            "column_id" => $this->column->id
          ]
        ],
        [
          "id" => $tasks[1]->id,
          "description" => $tasks[1]->description,
          "position" => $tasks[1]->position,
          "createdAt" => $tasks[1]->created_at->diffForHumans(),
          "relations" => [
            "column_id" => $this->column->id
          ]
        ]
      ]
    ]);
});
