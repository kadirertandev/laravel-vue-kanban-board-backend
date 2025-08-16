<?php

use App\Http\Controllers\Api\V1\BoardController as BoardControllerV1;
use App\Http\Controllers\Api\V1\ColumnController as ColumnControllerasV1;
use App\Http\Controllers\Api\V1\TaskController as TaskControllerasV1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth:sanctum"])
  ->get('/user', fn(Request $request) => $request->user());

Route::prefix("v1")
  ->middleware(["auth:sanctum"])
  ->group(function () {
    Route::apiResource("boards", BoardControllerV1::class);

    Route::apiResource("boards.columns", ColumnControllerasV1::class)->shallow();
    Route::put("columns/{column}/move", [ColumnControllerasV1::class, "move"])->name("boards.columns.move");

    Route::apiResource("columns.tasks", TaskControllerasV1::class)->shallow();
    Route::put("tasks/{task}/move", [TaskControllerasV1::class, "move"])->name("boards.columns.tasks.move");
  });