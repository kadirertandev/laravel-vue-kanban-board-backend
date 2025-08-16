<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoardResource;
use App\Models\Board;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BoardController extends Controller
{
  use AuthorizesRequests;

  public function index(Request $request)
  {
    $boards = $request->user()->boards()->latest()->withConditionals($request)->get();

    return BoardResource::collection($boards);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $board = $request->user()->boards()->create([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return response()->json([
      "status" => "success",
      "message" => "Board created successfully!",
      "data" => new BoardResource($board)
    ], 201);
  }

  public function show(Request $request, Board $board)
  {
    $this->authorize("view", $board);

    return new BoardResource($board);
  }

  public function update(Request $request, Board $board)
  {
    $this->authorize("update", $board);

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

  public function destroy(Request $request, Board $board)
  {
    $this->authorize("delete", $board);

    $board->delete();

    return response()->json([
      "status" => "success",
      "message" => "Board deleted successfully",
    ], 204);
  }
}
