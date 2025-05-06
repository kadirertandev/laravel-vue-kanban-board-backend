<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;

class TaskController extends Controller
{
  public function index(Request $request, $boardId, $columnId)
  {
    $tasks = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId)->tasks()->get();

    return TaskResource::collection($tasks);
  }

  public function store(Request $request, $boardId, $columnId)
  {
    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId)->tasks()->create([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return response()->json([
      "status" => "success",
      "message" => "Task created successfully!"
    ], 201);
  }

  public function show(Request $request, $boardId, $columnId, $taskId)
  {
    $task = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId)->tasks()->findOrFail($taskId);

    return new TaskResource($task);
  }

  public function update(Request $request, $boardId, $columnId, $taskId)
  {
    $task = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId)->tasks()->findOrFail($taskId);

    $validated = $request->validate([
      "title" => ["required", "string", "max:100"],
      "description" => ["required", "string", "max:255"]
    ]);

    $task->update([
      "title" => $validated["title"],
      "description" => $validated["description"]
    ]);

    return new TaskResource($task);
  }

  public function destroy(Request $request, $boardId, $columnId, $taskId)
  {
    $task = $request->user()->boards()->findOrFail($boardId)->columns()->findOrFail($columnId)->tasks()->findOrFail($taskId);

    $task->delete();

    return response()->json([
      "status" => "success",
      "message" => "Task deleted successfully",
    ], 204);
  }
}
