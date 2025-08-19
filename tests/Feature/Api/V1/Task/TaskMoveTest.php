<?php

use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);
  $this->task = createTaskForColumn(column: $this->column);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/tasks/{$this->task->id}/move";
});


it('denies unauthenticated users to move any task', function () {
  $response = $this->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1000
  ]);

  $response->assertStatus(401);
});

it('denies users to move tasks they do not own', function () {
  $response = $this->actingAs($this->user2)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1000
  ]);

  $response->assertStatus(403);
});

it('allows users to move tasks they do own inside same column', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1000
  ]);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->task->id,
      "description" => $this->task->description,
      "position" => 1000,
      "createdAt" => $this->task->created_at->diffForHumans(),
      "relations" => [
        "column_id" => $this->column->id
      ]
    ]);

  $this->assertDatabaseHas("tasks", [
    "id" => $this->task->id,
    "column_id" => $this->column->id,
    "position" => 1000
  ]);
});

it('allows users to move tasks they do own between different columns', function () {
  $column2 = createColumnForBoard($this->board, 1);

  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $column2->id,
    "position" => 1000
  ]);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->task->id,
      "description" => $this->task->description,
      "position" => 1000,
      "createdAt" => $this->task->created_at->diffForHumans(),
      "relations" => [
        "column_id" => $column2->id
      ]
    ]);

  $this->assertDatabaseHas("tasks", [
    "id" => $this->task->id,
    "column_id" => $column2->id,
    "position" => 1000
  ]);
  $this->assertDatabaseMissing("tasks", [
    "id" => $this->task->id,
    "column_id" => $this->column->id,
    "position" => 1000
  ]);
});

it('denies users to move tasks to a column they do not own', function () {
  $board2 = createBoardForUser($this->user2, 1);
  $column2 = createColumnForBoard($board2, 1);

  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $column2->id,
    "position" => 1000
  ]);

  $response->assertStatus(403);

  $this->assertDatabaseHas("tasks", [
    "id" => $this->task->id,
    "column_id" => $this->column->id,
    "position" => 60000
  ]);
  $this->assertDatabaseMissing("tasks", [
    "id" => $this->task->id,
    "column_id" => $column2->id,
    "position" => 1000
  ]);
});

it('returns expected json structure on success', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1000
  ]);

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "description",
        "position",
        "createdAt",
        "relations" => [
          "column_id"
        ]
      ]
    ]);
});

it('returns error when fields are invalid', function () {
  $response = $this->actingAs($this->user)->putJson($this->endPoint);

  $response->assertStatus(422)
    ->assertInvalid([
      "position" => "The position field is required.",
      "fromColumn" => "The from column field is required.",
      "toColumn" => "The to column field is required."
    ])
    ->assertJsonStructure([
      "message",
      "errors" => [
        "fromColumn" => [],
        "toColumn" => [],
        "position" => []
      ]
    ])->assertJson(function (AssertableJson $json) {
      return $json->where("message", "The from column field is required. (and 2 more errors)")
        ->has("errors.fromColumn")->where("errors.fromColumn.0", "The from column field is required.")
        ->has("errors.toColumn")->where("errors.toColumn.0", "The to column field is required.")
        ->has("errors.position")->where("errors.position.0", "The position field is required.");
    });
});

it('rounds task position to 5 decimal places when moving', function () {
  #1
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1.123456
  ]);
  $response->assertStatus(200);
  $this->assertDatabaseHas("tasks", [
    "id" => $this->column->id,
    "column_id" => $this->column->id,
    "position" => 1.12346 //passes because round(1.123456, 5) equals 1.12346
  ]);

  #2
  $response = $this->actingAs($this->user)->putJson($this->endPoint, [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 1.123454
  ]);
  $response->assertStatus(200);
  $this->assertDatabaseHas("tasks", [
    "id" => $this->column->id,
    "column_id" => $this->column->id,
    "position" => 1.12345 //passes because round(1.123454, 5) equals 1.12345
  ]);
});

it('repositions tasks when position of a column goes below minimum', function () {
  $this->column = createColumnForBoard($this->board);
  $tasks = createTaskForColumn($this->column, 2);

  $response = $this->actingAs($this->user)->putJson("{$this->apiPrefix}/tasks/{$tasks[1]->id}/move", [
    "fromColumn" => $this->column->id,
    "toColumn" => $this->column->id,
    "position" => 0.00001
  ]);

  $response->assertStatus(200)
    ->assertExactJson([
      "data" => [
        "id" => $tasks[1]->id,
        "description" => $tasks[1]->description,
        "position" => 60000,
        "createdAt" => $tasks[1]->created_at->diffForHumans(),
        "relations" => [
          "column_id" => $this->column->id
        ]
      ]
    ]);

  $this->assertDatabaseHas("tasks", [
    "id" => $tasks[1]->id,
    "column_id" => $this->column->id,
    "position" => 60000
  ]);

  $this->assertDatabaseHas("tasks", [
    "id" => $tasks[0]->id,
    "column_id" => $this->column->id,
    "position" => 120000
  ]);
});