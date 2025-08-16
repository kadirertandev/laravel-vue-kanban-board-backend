<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
  "middleware" => ["auth:sanctum"]
], function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });

  Route::apiResource("boards", BoardController::class);

  Route::apiResource("boards.columns", ColumnController::class)->shallow();
  Route::put("columns/{column}/move", [ColumnController::class, "move"])->name("boards.columns.move");

  Route::apiResource("columns.tasks", TaskController::class)->shallow();
  Route::put("tasks/{task}/move", [TaskController::class, "move"])->name("boards.columns.tasks.move");
});