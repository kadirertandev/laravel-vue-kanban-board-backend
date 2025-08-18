<?php

use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
  $this->user = createUser();
  $this->apiPrefix = "/api/v1";
});

test('unauthenticated user cannot create board', function () {
  $response = $this->postJson("{$this->apiPrefix}/boards", dummyBoardData(1));

  $response->assertStatus(401);
});

test('authenticated user can create board', function () {
  $data = dummyBoardData(1);

  $response = $this->actingAs($this->user)->postJson("{$this->apiPrefix}/boards", $data);

  $response->assertStatus(201);
  $this->assertDatabaseHas("boards", [
    "user_id" => $this->user->id,
    ...$data
  ]);
});

it('returns expected json structure on success', function () {
  $data = dummyBoardData(1);

  $response = $this->actingAs($this->user)->postJson("{$this->apiPrefix}/boards", $data);

  $response->assertStatus(201)
    ->assertJsonStructure([
      "status",
      "message",
      "data" => [
        "id",
        "title",
        "description",
        "createdAtFrontend",
        "createdAt",
      ]
    ]);
});

it('returns error when fields are invalid', function () {
  $data = [
    "title" => "",
    "description" => ""
  ];

  $response = $this->actingAs($this->user)->postJson("{$this->apiPrefix}/boards", $data);

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
