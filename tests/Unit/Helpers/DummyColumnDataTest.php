<?php

it('throws exception when amount is zero', function () {
  dummyColumnData(0);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it('throws exception when amount is negative', function () {
  dummyColumnData(-4);
})->throws(Exception::class, "Amount must be bigger than or equal to 1");

it("returns a single column array when amount is 1", function () {
  $data = dummyColumnData(1);

  expect($data)->toBeArray()
    ->toHaveKeys(["title", "description"])
    ->and($data["title"])->toBe("Column 1")
    ->and($data["description"])->toBe("Column 1 Description");
});

it('returns multiple columns when amount is bigger than 1', function () {
  $data = dummyColumnData(3);

  expect($data)->toBeArray()
    ->toHaveKeys([0, 1, 2]);

  expect($data[0])->toHaveKeys(["title", "description"])
    ->and($data[0]["title"])->toBe("Column 1")
    ->and($data[0]["description"])->toBe("Column 1 Description");

  expect($data[1])->toHaveKeys(["title", "description"])
    ->and($data[1]["title"])->toBe("Column 2")
    ->and($data[1]["description"])->toBe("Column 2 Description");

  expect($data[2])->toHaveKeys(["title", "description"])
    ->and($data[2]["title"])->toBe("Column 3")
    ->and($data[2]["description"])->toBe("Column 3 Description");
});
