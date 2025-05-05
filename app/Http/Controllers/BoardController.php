<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoardResource;
use Illuminate\Http\Request;

class BoardController extends Controller
{
  public function index(Request $request)
  {
    $boards = $request->user()->boards()->latest()->get();

    return BoardResource::collection($boards);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $request->user()->boards()->create([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return response()->json([
      "status" => "success",
      "message" => "Board created successfully!"
    ], 201);
  }

  public function show(Request $request, $id)
  {
    $board = $request->user()->boards()->findOrFail($id);

    return new BoardResource($board);
  }

  public function update(Request $request, $id)
  {
    $board = $request->user()->boards()->findOrFail($id);

    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $board->update([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return new BoardResource($board);
  }

  public function destroy(Request $request, $id)
  {
    $board = $request->user()->boards()->findOrFail($id);

    $board->delete();

    return response()->json([
      "status" => "success",
      "message" => "Board deleted successfully",
    ], 204);
  }
}
