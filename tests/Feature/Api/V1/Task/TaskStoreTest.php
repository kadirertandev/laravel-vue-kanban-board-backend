<?php

use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard(board: $this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}/tasks";
});

it('does not allow unauthenticated users to create a task', function () {
  $response = $this->postJson($this->endPoint, dummyTaskData(1));

  $response->assertStatus(401);
});

it('allows authenticated users to create a task', function () {
  $data = dummyTaskData(1);

  $response = $this->actingAs($this->user)->postJson($this->endPoint, $data);

  $response->assertStatus(201)
    ->assertJsonFragment([
      "status" => "success",
      "message" => "Task created successfully!"
    ])
    ->assertJsonFragment([
      "description" => $data["description"],
      "position" => 60000,
      "createdAt" => "0 seconds ago",
      "relations" => [
        "column_id" => $this->column->id
      ]
    ]);
  $this->assertDatabaseHas("tasks", [
    "column_id" => $this->column->id,
    ...$data
  ]);
});

it('returns expected json structure on success', function () {
  $data = dummyColumnData(1);

  $response = $this->actingAs($this->user)->postJson($this->endPoint, $data);

  $response->assertStatus(201)
    ->assertJsonStructure([
      "status",
      "message",
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
  $data = [
    "description" => ""
  ];

  $response = $this->actingAs($this->user)->postJson($this->endPoint, $data);

  $response->assertStatus(422)
    ->assertInvalid([
      "description" => "The description field is required."
    ])
    ->assertJson(function (AssertableJson $json) {
      return $json->where("message", "The description field is required.")
        ->has("errors.description")
        ->where("errors.description.0", "The description field is required.")
        ->etc();
    });
});