<?php

beforeEach(function () {
  $this->user = createUser();
  $this->user2 = createUser();
  $this->board = createBoardForUser($this->user);
  $this->apiPrefix = "/api/v1";
  $this->endPoint = "{$this->apiPrefix}/boards/{$this->board->id}/columns";
});

it('denies access to the columns index endpoint for unauthenticated users', function () {
  $response = $this->getJson($this->endPoint);

  $response->assertStatus(401);
});

it('denies access to the columns index endpoint when user does not own the columns', function () {
  createColumnForBoard($this->board, 3);
  $response = $this->actingAs($this->user2)->getJson($this->endPoint);

  $response->assertStatus(403);
});

it('allows access to the columns index endpoint when user does own the columns', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200);
});

it('returns empty data', function () {
  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertExactJson(["data" => []])
    ->assertJsonCount(0, "data");
});

it('returns the columns belongs to authenticated user', function () {
  $columns = createColumnForBoard($this->board, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonFragment([
      "title" => $columns[0]->title,
      "description" => $columns[0]->description
    ])
    ->assertJsonCount(3, "data");
});

it('returns expected json structure for single board', function () {
  $column = createColumnForBoard($this->board, 1);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(1, "data")
    ->assertJsonFragment([
      "title" => $column->title,
      "description" => $column->description
    ])
    ->assertJsonStructure([
      "data" => [
        [
          "id",
          "title",
          "description",
          "position",
          "createdAt",
        ]
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $column->id,
          "title" => $column->title,
          "description" => $column->description,
          "position" => $column->position,
          "createdAt" => $column->created_at->diffForHumans(),
        ]
      ]
    ]);
});

it('returns expected json structure for multiple boards', function () {
  $columns = createColumnForBoard($this->board, 3);

  $response = $this->actingAs($this->user)->getJson($this->endPoint);

  $response->assertStatus(200)
    ->assertJsonCount(3, "data")
    ->assertJsonFragment([
      "title" => $columns[0]->title,
      "description" => $columns[0]->description
    ])
    ->assertJsonStructure([
      "data" => [
        "*" => [
          "id",
          "title",
          "description",
          "position",
          "createdAt",
        ]
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $columns[0]->id,
          "title" => $columns[0]->title,
          "description" => $columns[0]->description,
          "position" => $columns[0]->position,
          "createdAt" => $columns[0]->created_at->diffForHumans(),
        ],
        [
          "id" => $columns[1]->id,
          "title" => $columns[1]->title,
          "description" => $columns[1]->description,
          "position" => $columns[1]->position,
          "createdAt" => $columns[1]->created_at->diffForHumans(),
        ],
        [
          "id" => $columns[2]->id,
          "title" => $columns[2]->title,
          "description" => $columns[2]->description,
          "position" => $columns[2]->position,
          "createdAt" => $columns[2]->created_at->diffForHumans(),
        ]
      ]
    ]);
});

it('returns expected json structure when relationships are requested', function () {
  $columns = createColumnForBoard($this->board, 2);
  $column1Task = createTaskForColumn($columns[0]);

  $route = route("boards.columns.index", [
    "board" => $this->board,
    "withColumnTasks" => true,
    "withColumnBoard" => true,
    "withTaskColumn" => true
  ]);
  $response = $this->actingAs($this->user)->getJson($route);

  $response->assertStatus(200)
    ->assertJsonCount(2, "data")
    ->assertJsonFragment([
      "title" => $columns[0]->title,
      "description" => $columns[0]->description
    ])
    ->assertJsonStructure([
      "data" => [
        "*" => [
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
      ]
    ])
    ->assertExactJson([
      "data" => [
        [
          "id" => $columns[0]->id,
          "title" => $columns[0]->title,
          "description" => $columns[0]->description,
          "position" => $columns[0]->position,
          "createdAt" => $columns[0]->created_at->diffForHumans(),
          "tasks" => [
            [
              "id" => $column1Task->id,
              "description" => $column1Task->description,
              "position" => $column1Task->position,
              "createdAt" => $column1Task->created_at->diffForHumans(),
              "relations" => [
                "column_id" => $columns[0]->id
              ],
            ]
          ],
          "relations" => [
            "board_id" => $this->board->id
          ]
        ],
        [
          "id" => $columns[1]->id,
          "title" => $columns[1]->title,
          "description" => $columns[1]->description,
          "position" => $columns[1]->position,
          "createdAt" => $columns[1]->created_at->diffForHumans(),
          "tasks" => [], //didnt create task for this column on purpose to prove that structure works even when there is no tasks too
          "relations" => [
            "board_id" => $this->board->id
          ]
        ]
      ]
    ]);
});