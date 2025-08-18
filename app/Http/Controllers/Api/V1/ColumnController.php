<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Column\MoveColumnRequest;
use App\Http\Requests\Api\V1\Column\StoreColumnRequest;
use App\Http\Requests\Api\V1\Column\UpdateColumnRequest;
use App\Http\Resources\V1\ColumnResource as ColumnResourceV1;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
  use AuthorizesRequests;

  public function index(Request $request, Board $board)
  {
    $this->authorize("viewAny", [Column::class, $board]);

    $columns = $board->columns()->withConditionals($request)->get();

    return ColumnResourceV1::collection($columns);
  }

  public function store(StoreColumnRequest $storeColumnRequest, Board $board)
  {
    $this->authorize("create", [Column::class, $board]);

    $validated = $storeColumnRequest->validated();

    $column = $board->columns()->create($validated);

    return response()->json([
      "status" => "success",
      "message" => "Column created successfully!",
      "data" => new ColumnResourceV1($column->load(["board", "tasks", "tasks.column"]))
    ], 201);
  }

  public function show(Request $request, Column $column)
  {
    $this->authorize("view", $column);

    return new ColumnResourceV1($column);
  }

  public function update(UpdateColumnRequest $updateColumnRequest, Column $column)
  {
    $this->authorize("update", $column);

    $validated = $updateColumnRequest->validated();

    $column->update($validated);

    return new ColumnResourceV1($column);
  }

  public function destroy(Request $request, Column $column)
  {
    $this->authorize("delete", $column);

    $column->delete();

    return response()->json([
      "status" => "success",
      "message" => "Column deleted successfully",
    ], 204);
  }

  public function move(MoveColumnRequest $moveColumnRequest, Column $column)
  {
    $this->authorize("update", $column);

    $validated = $moveColumnRequest->validated();

    $column->update($validated);

    $column->refresh(); //refresh in case positions have been reset

    return new ColumnResourceV1($column);
  }
}
