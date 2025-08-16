<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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

  public function store(Request $request, Board $board)
  {
    $this->authorize("create", [Column::class, $board]);

    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $column = $board->columns()->create([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

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

  public function update(Request $request, Column $column)
  {
    $this->authorize("update", $column);

    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $column->update([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

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

  public function move(Request $request, Column $column)
  {
    $this->authorize("update", $column);

    $request->validate([
      'position' => ['required', 'numeric']
    ]);

    $column->update([
      'position' => round(request('position'), 5)
    ]);

    return new ColumnResourceV1($column);
  }
}
