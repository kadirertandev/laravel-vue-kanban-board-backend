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
      "createdAtFrontend" => $this->created_at->diffForHumans(),
      "createdAt" => $this->created_at,
      "columns" => ColumnResource::collection($this->whenLoaded("columns"))
    ];
  }
}
