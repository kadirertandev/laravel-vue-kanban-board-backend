<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      "id" => $this->id,
      "title" => $this->title,
      "description" => $this->description,
      "createdAt" => $this->created_at->diffForHumans(),
      "columns" => ColumnResource::collection($this->whenLoaded("columns", function () {
        return $this->columns
          ->keyBy('id')
          ->map(fn($column) => new ColumnResource($column));
      }))
    ];
  }
}
