<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
  "middleware" => ["auth:sanctum"]
], function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });

  Route::apiResource("boards", BoardController::class);

  Route::apiResource("boards.columns", ColumnController::class);
});