<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->apiPrefix = "/api/v1";
});

it('denies unauthenticated users to delete any board', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->deleteJson("{$this->apiPrefix}/boards/{$board->id}", dummyBoardData(1));

  $response->assertStatus(401);
});

it('denies users to delete boards they do not own', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user2)->deleteJson("{$this->apiPrefix}/boards/{$board->id}", dummyBoardData(1));

  $response->assertStatus(403);
});

it('allows users to delete boards they do own', function () {
  $board = createBoardForUser($this->user, 1);

  $response = $this->actingAs($this->user)->deleteJson("{$this->apiPrefix}/boards/{$board->id}");

  $response->assertStatus(204)
    ->assertNoContent();

  $this->assertDatabaseMissing("boards", [
    "user_id" => $this->user->id,
    "title" => $board->title,
    "description" => $board->description,
  ]);
});