<?php

namespace App\Http\Controllers;

use App\Http\Resources\ColumnResource;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
  public function index(Request $request, $boardId)
  {
    $columns = $request->user()->boards()->findOrFail($boardId)->columns()->get();

    return ColumnResource::collection($columns);
  }

  public function store(Request $request, $boardId)
  {
    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $request->user()->boards()->findOrFail($boardId)->columns()->create([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return response()->json([
      "status" => "success",
      "message" => "Column created successfully!"
    ], 201);
  }

  public function show(Request $request, $boardId, $columnId)
  {
    $column = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId);

    return new ColumnResource($column);
  }

  public function update(Request $request, $boardId, $columnId)
  {
    $column = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId);

    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $column->update([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return new ColumnResource($column);
  }

  public function destroy(Request $request, $boardId, $columnId)
  {
    $column = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId);

    $column->delete();

    return response()->json([
      "status" => "success",
      "message" => "Column deleted successfully",
    ], 204);
  }
}
