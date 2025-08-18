<?php

beforeEach(function () {
  $this->user = createUser();
  $this->board = createBoardForUser($this->user);
});

it('throws exception when amount is zero', function () {
  createColumnForBoard($this->board, 0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  createColumnForBoard($this->board, -4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('creates 1 column for given board when amount is 1', function () {
  $column = createColumnForBoard($this->board, 1);

  expect($this->board->columns()->count())->toBe(1);
  $this->assertDatabaseHas("columns", [
    "board_id" => $this->board->id,
    "title" => "Column 1",
    "description" => "Column 1 Description"
  ]);
});

it('creates multiple columns for given board when amount is bigger than 1', function () {
  createColumnForBoard($this->board, 3);
  $columnsArray = [
    ["board_id" => $this->board->id, "title" => "Column 1", "description" => "Column 1 Description"],
    ["board_id" => $this->board->id, "title" => "Column 2", "description" => "Column 2 Description"],
    ["board_id" => $this->board->id, "title" => "Column 3", "description" => "Column 3 Description"]
  ];

  expect($this->board->columns()->count())->toBe(3);
  foreach ($columnsArray as $data) {
    $this->assertDatabaseHas(
      "columns",
      $data
    );
  }
});
