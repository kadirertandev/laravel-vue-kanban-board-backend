<?php

use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = createUser();
  $this->board = createBoardForUser($this->user);
  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/boards/{$this->board->id}/columns";
});

it('does not allow unauthenticated users to create a column', function () {
  $response = $this->postJson($this->endPoint, dummyColumnData(1));

  $response->assertStatus(401);
});

it('allows authenticated users to create a column', function () {
  $data = dummyColumnData(1);

  $response = $this->actingAs($this->user)->postJson($this->endPoint, $data);

  $response->assertStatus(201);
  $this->assertDatabaseHas("columns", [
    "board_id" => $this->board->id,
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
        "title",
        "description",
        "position",
        "createdAt",
        "tasks" => [
          "*" => [
            "id",
            "description",
            "position",
            "createdAt",
            "relations" => [
              "column_id"
            ],
          ]
        ],
        "relations" => [
          "board_id"
        ]
      ]
    ]);
});

it('returns error when fields are invalid', function () {
  $data = [
    "title" => "",
    "description" => ""
  ];

  $response = $this->actingAs($this->user)->postJson($this->endPoint, $data);

  $response->assertStatus(422)
    ->assertInvalid([
      "title" => "The title field is required.",
      "description" => "The description field is required."
    ])
    ->assertJson(function (AssertableJson $json) {
      return $json->where("message", "The title field is required. (and 1 more error)")
        ->has("errors.title")
        ->where("errors.title.0", "The title field is required.")
        ->has("errors.description")
        ->where("errors.description.0", "The description field is required.")
        ->etc();
    });
});