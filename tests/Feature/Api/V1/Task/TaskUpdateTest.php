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

it('denies unauthenticated users to update any task', function () {
  $response = $this->putJson($this->endPoint, dummyTaskData(1));

  $response->assertStatus(401);
});

it('denies users to update tasks they do not own', function () {
  $response = $this->actingAs($this->user2)->putJson($this->endPoint, dummyBoardData(1));

  $response->assertStatus(403);
});

it('allows users to update tasks they do own', function () {
  $updateData = [
    "description" => "Task 1 is now updated (description)",
  ];

  $response = $this->actingAs($this->user)->putJson($this->endPoint, $updateData);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->task->id,
      "description" => $updateData["description"],
      "position" => $this->task->position,
      "createdAt" => $this->task->created_at->diffForHumans()
    ]);

  $this->assertDatabaseHas("tasks", [
    "column_id" => $this->column->id,
    ...$updateData
  ]);
});

it('returns expected json structure on success', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, dummyTaskData(1));

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "description",
        "position",
        "createdAt"
      ]
    ])->assertJsonMissingPath("data.relations");
});

it('returns error when fields are invalid', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, []);

  $response->assertStatus(422)
    ->assertInvalid([
      "description" => "The description field is required.",
    ])
    ->assertJsonStructure([
      "message",
      "errors" => [
        "description" => []
      ]
    ]);
});