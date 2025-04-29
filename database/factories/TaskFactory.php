<?php

namespace Database\Factories;

use App\Models\Column;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    static $position = 1;

    return [
      "column_id" => Column::factory(),
      "title" => fake()->title,
      "description" => fake()->text(100),
      "position" => $position++
    ];
  }
}
