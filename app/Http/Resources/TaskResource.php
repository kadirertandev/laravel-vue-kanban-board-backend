<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
      "description" => $this->description,
      "position" => $this->position,
      "createdAt" => $this->created_at->diffForHumans(),
    ];

    if ($this->relationLoaded('column')) {
      $data['relations'] = [
        'column_id' => $this->column->id,
      ];
    }

    return $data;
  }
}
