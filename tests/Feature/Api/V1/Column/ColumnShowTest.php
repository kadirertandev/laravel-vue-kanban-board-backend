<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard($this->board);

  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/columns/{$this->column->id}";
});

it('denies access to the columns show endpoint for unauthenticated users', function () {
  $response = $this->getJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies access to the columns show endpoint when user does not own the column', function () {
  $response = $this->actingAs($this->user2)->getJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows access to the columns show endpoint when user does own the column', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "id" => $this->column->id,
      "title" => $this->column->title,
      "description" => $this->column->description,
      "position" => $this->column->position,
      "createdAt" => $this->column->created_at->diffForHumans()
    ]);
});

it('returns expected json structure for the column', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonStructure([
      "data" => [
        "id",
        "title",
        "description",
        "position",
        "createdAt"
      ]
    ]);
});
