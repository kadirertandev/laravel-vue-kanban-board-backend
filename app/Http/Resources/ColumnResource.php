<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColumnResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $data = [
      "id" => $this->id,
      "title" => $this->title,
      "position" => $this->position,
      "createdAt" => $this->created_at->diffForHumans(),
      "tasks" => TaskResource::collection($this->whenLoaded("tasks")),
    ];

    if ($this->relationLoaded('board')) {
      $data['relations'] = [
        'board_id' => $this->board->id,
      ];
    }

    return $data;
  }
}
