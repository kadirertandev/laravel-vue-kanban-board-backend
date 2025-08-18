<?php

beforeEach(function () {
  $this->user = createUser();
  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/boards";
});

it('denies access to the board index endpoint for unauthenticated users', function () {
  $response = $this->getJson($this->endPoint);

  $response->assertStatus(401);
});

it('allows access to the board index endpoint for authenticated users', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200);
});

it('returns empty data', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertExactJson(["data" => []])
    ->assertJsonCount(0, "data");
});

it('returns the boards belongs to authenticated user', function () {
  $boards = createBoardForUser($this->user, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(3, "data")
    ->assertJsonFragment([
      "title" => $boards[0]->title,
      "description" => $boards[0]->description
    ]);
});

it('returns expected json structure for single board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "title" => $board->title,
      "description" => $board->description
    ])
    ->assertJsonStructure([
      "data" => [
        [
          "id",
          "title",
          "description",
          "createdAtFrontend",
          "createdAt"
        ]
      ]
    ]);
});

it('returns expected json structure for multiple boards', function () {
  $boards = createBoardForUser($this->user, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(3, "data")
    ->assertJsonFragment([
      "title" => $boards[0]->title,
      "description" => $boards[0]->description
    ])
    ->assertJsonStructure([
      "data" => [
        "*" => [
          "id",
          "title",
          "description",
          "createdAtFrontend",
          "createdAt"
        ]
      ]
    ]);
});
