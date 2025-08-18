<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->apiPrefix = "/api/v1";
});

it('denies access to the board show endpoint for unauthenticated users', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->getJson("{$this->apiPrefix}/boards/{$board->id}");

  $response->assertStatus(401);
});

it('denies access to the board show endpoint when user does not own the board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user2)->getJson("{$this->apiPrefix}/boards/{$board->id}");

  $response->assertStatus(403);
});

it('allows access to the board show endpoint when user does own the board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->getJson("{$this->apiPrefix}/boards/{$board->id}");


  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $board->id,
      "title" => $board->title,
      "description" => $board->description,
      "createdAtFrontend" => $board->created_at->diffForHumans(),
      "createdAt" => $board->created_at
    ]);
});

it('returns expected json structure for the board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->getJson("{$this->apiPrefix}/boards/{$board->id}");

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "title",
        "description",
        "createdAtFrontend",
        "createdAt"
      ]
    ]);
});