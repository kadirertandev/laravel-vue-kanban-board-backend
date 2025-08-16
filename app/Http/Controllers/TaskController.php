<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Column;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
  use AuthorizesRequests;

  public function index(Request $request, Column $column)
  {
    $this->authorize("viewAny", [Task::class, $column]);

    $tasks = $column->tasks()->withConditionals($request)->get();

    return TaskResource::collection($tasks);
  }

  public function store(Request $request, Column $column)
  {
    $this->authorize("create", [Task::class, $column]);

    $validated = $request->validate([
      "description" => ["required", "string", "max:255"],
    ]);

    $task = $column->tasks()->create([
      "description" => $validated["description"]
    ]);

    return response()->json([
      "status" => "success",
      "message" => "Task created successfully!",
      "data" => new TaskResource($task->load("column"))
    ], 201);
  }

  public function show(Request $request, Task $task)
  {
    $this->authorize("view", $task);

    return new TaskResource($task);
  }

  public function update(Request $request, Task $task)
  {
    $this->authorize("update", $task);

    $validated = $request->validate([
      "description" => ["required", "string", "max:255"]
    ]);

    $task->update([
      "description" => $validated["description"]
    ]);

    return new TaskResource($task);
  }

  public function destroy(Request $request, Task $task)
  {
    $this->authorize("update", $task);

    $task->delete();

    return response()->json([
      "status" => "success",
      "message" => "Task deleted successfully",
    ], 204);
  }

  public function move(Request $request, Task $task)
  {
    $this->authorize("update", $task);

    $request->validate([
      'fromColumn' => ['required', 'exists:columns,id'],
      'toColumn' => ['required', 'exists:columns,id'],
      'position' => ['required', 'numeric'],
    ]);

    $task->update([
      'column_id' => request("toColumn"),
      'position' => round(request('position'), 5)
    ]);

    return new TaskResource($task->load("column"));
  }
}
