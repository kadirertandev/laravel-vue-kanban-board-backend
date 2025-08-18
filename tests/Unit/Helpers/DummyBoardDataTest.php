<?php

it('throws exception when amount is zero', function () {
  dummyBoardData(0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  dummyBoardData(-4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it("returns a single board array when amount is 1", function () {
  $data = dummyBoardData(1);

  expect($data)->toBeArray();
  expect($data)->toHaveKeys(["title", "description"])
    ->and($data["title"])->toBe("Board 1")
    ->and($data["description"])->toBe("Board 1 Description");
});

it('returns multiple boards when amount is bigger than 1', function () {
  $data = dummyBoardData(3);

  expect($data)->toBeArray();
  expect($data)->toHaveKeys([0, 1, 2]);

  expect($data[0])->toHaveKeys(["title", "description"])
    ->and($data[0]["title"])->toBe("Board 1")
    ->and($data[0]["description"])->toBe("Board 1 Description");

  expect($data[1])->toHaveKeys(["title", "description"])
    ->and($data[1]["title"])->toBe("Board 2")
    ->and($data[1]["description"])->toBe("Board 2 Description");

  expect($data[2])->toHaveKeys(["title", "description"])
    ->and($data[2]["title"])->toBe("Board 3")
    ->and($data[2]["description"])->toBe("Board 3 Description");
});
