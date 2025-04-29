<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Board>
 */
class BoardFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      // "user_id" => User::factory(),
      "user_id" => 1,
      "title" => fake()->title,
      "description" => fake()->text(100)
    ];
  }
}
