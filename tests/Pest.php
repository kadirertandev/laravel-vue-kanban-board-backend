<?php

use App\Models\Board;
use App\Models\Column;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
  ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
  ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
  return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createUser()
{
  return User::factory()->create();
}

function createBoardForUser(User $user, int $amount = 1)
{
  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  if ($amount === 1)
    return $user->boards()->create(dummyBoardData($amount));

  return collect(dummyBoardData($amount))->map(function ($data) use ($user) {
    return $user->boards()->create($data);
  });
}

function dummyBoardData(int $amount)
{
  $data = [];

  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  $title = "Board ";
  $descriptionB = "Board ";
  $descriptionE = " Description";


  for ($i = 1; $i <= $amount; $i++) {
    $data[] = [
      "title" => $title . $i,
      "description" => $descriptionB . $i . $descriptionE
    ];
  }

  return $amount === 1 ? $data[0] : $data;
}

function createColumnForBoard(Board $board, int $amount = 1)
{
  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  if ($amount === 1)
    return $board->columns()->create(dummyColumnData($amount));

  return collect(dummyColumnData($amount))->map(function ($data) use ($board) {
    return $board->columns()->create($data);
  });
}

function dummyColumnData(int $amount)
{
  $data = [];

  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  $title = "Column ";
  $descriptionB = "Column ";
  $descriptionE = " Description";

  for ($i = 1; $i <= $amount; $i++) {
    $data[] = [
      "title" => $title . $i,
      "description" => $descriptionB . $i . $descriptionE
    ];
  }

  return $amount === 1 ? $data[0] : $data;
}

function createTaskForColumn(Column $column, int $amount = 1)
{
  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  if ($amount === 1)
    return $column->tasks()->create(dummyTaskData($amount));

  return collect(dummyTaskData($amount))->map(function ($data) use ($column) {
    return $column->tasks()->create($data);
  });
}

function dummyTaskData(int $amount)
{
  $data = [];

  if ($amount < 1)
    throw new Exception("Amount must be bigger than or equal to 1");

  $descriptionB = "Task ";
  $descriptionE = " Description";

  for ($i = 1; $i <= $amount; $i++) {
    $data[] = [
      "description" => $descriptionB . $i . $descriptionE
    ];
  }

  return $amount === 1 ? $data[0] : $data;
}


