<?php

it('throws exception when amount is zero', function () {
  dummyTaskData(0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  dummyTaskData(-4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it("returns a single task array when amount is 1", function () {
  $data = dummyTaskData(1);

  expect($data)->toBeArray()
    ->toHaveKeys(["description"])
    ->and($data["description"])->toBe("Task 1 Description");
});

it('returns multiple tasks when amount is bigger than 1', function () {
  $data = dummyTaskData(3);

  expect($data)->toBeArray()
    ->toHaveKeys([0, 1, 2]);

  expect($data[0])->toHaveKeys(["description"])
    ->and($data[0]["description"])->toBe("Task 1 Description");

  expect($data[1])->toHaveKeys(["description"])
    ->and($data[1]["description"])->toBe("Task 2 Description");

  expect($data[2])->toHaveKeys(["description"])
    ->and($data[2]["description"])->toBe("Task 3 Description");
});
