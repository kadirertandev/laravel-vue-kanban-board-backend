<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Task\MoveTaskRequest;
use App\Http\Requests\Api\V1\Task\StoreTaskRequest;
use App\Http\Requests\Api\V1\Task\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource as TaskResourceV1;
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

    return TaskResourceV1::collection($tasks);
  }

  public function store(StoreTaskRequest $storeTaskRequest, Column $column)
  {
    $this->authorize("create", [Task::class, $column]);

    $validated = $storeTaskRequest->validated();

    $task = $column->tasks()->create($validated);

    return response()->json([
      "status" => "success",
      "message" => "Task created successfully!",
      "data" => new TaskResourceV1($task->load("column"))
    ], 201);
  }

  public function show(Request $request, Task $task)
  {
    $this->authorize("view", $task);

    return new TaskResourceV1($task);
  }

  public function update(UpdateTaskRequest $updateTaskRequest, Task $task)
  {
    $this->authorize("update", $task);

    $validated = $updateTaskRequest->validated();

    $task->update($validated);

    return new TaskResourceV1($task);
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

  public function move(MoveTaskRequest $moveTaskRequest, Task $task)
  {
    $this->authorize("update", $task);

    $validated = $moveTaskRequest->validated();

    $task->update($validated);

    $task->refresh();

    return new TaskResourceV1($task->load("column"));
  }
}
