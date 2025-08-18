<?php

beforeEach(function () {
  $this->user = createUser();
  $this->board = createBoardForUser($this->user);
  $this->column = createColumnForBoard($this->board);
});

it('throws exception when amount is zero', function () {
  createTaskForColumn($this->column, 0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  createTaskForColumn($this->column, -4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('creates 1 task for given column when amount is 1', function () {
  $column = createTaskForColumn($this->column, 1);

  expect($this->column->tasks()->count())->toBe(1);
  $this->assertDatabaseHas("tasks", [
    "column_id" => $this->column->id,
    "description" => "Task 1 Description"
  ]);
});

it('creates multiple tasks for given column when amount is bigger than 1', function () {
  createTaskForColumn($this->column, 3);
  $tasksArray = [
    ["Column_id" => $this->column->id, "description" => "Task 1 Description"],
    ["Column_id" => $this->column->id, "description" => "Task 2 Description"],
    ["Column_id" => $this->column->id, "description" => "Task 3 Description"]
  ];

  expect($this->column->tasks()->count())->toBe(3);
  foreach ($tasksArray as $data) {
    $this->assertDatabaseHas(
      "tasks",
      $data
    );
  }
});
