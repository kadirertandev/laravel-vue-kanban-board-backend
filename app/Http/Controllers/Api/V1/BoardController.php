<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Board\StoreBoardRequest;
use App\Http\Requests\Api\V1\Board\UpdateBoardRequest;
use App\Http\Resources\V1\BoardResource as BoardResourceV1;
use App\Models\Board;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BoardController extends Controller
{
  use AuthorizesRequests;

  public function index(Request $request)
  {
    $boards = $request->user()->boards()->latest()->withConditionals($request)->get();

    return BoardResourceV1::collection($boards);
  }

  public function store(StoreBoardRequest $storeBoardRequest)
  {
    $validated = $storeBoardRequest->validated();

    $board = $storeBoardRequest->user()->boards()->create($validated);

    return response()->json([
      "status" => "success",
      "message" => "Board created successfully!",
      "data" => new BoardResourceV1($board)
    ], 201);
  }

  public function show(Request $request, Board $board)
  {
    $this->authorize("view", $board);

    return new BoardResourceV1($board);
  }

  public function update(UpdateBoardRequest $updateBoardRequest, Board $board)
  {
    $this->authorize("update", $board);

    $validated = $updateBoardRequest->validated();

    $board->update($validated);

    return new BoardResourceV1($board);
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
