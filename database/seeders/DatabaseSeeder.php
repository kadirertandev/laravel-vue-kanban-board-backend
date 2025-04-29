<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Column;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    User::factory()->create([
      'name' => 'Test User',
      'email' => 'test@test.com',
      'password' => "asdfasdf"
    ]);

    // User::factory(10)->create();

    Board::factory()
      ->count(3)
      ->create()
      ->each(function ($board) {
        $columnPosition = 1;

        Column::factory()
          ->count(3)
          ->state(function () use ($board, &$columnPosition) {
            return [
              "board_id" => $board->id,
              "position" => $columnPosition++
            ];
          })
          ->create()
          ->each(function ($column) {
            $taskPosition = 1;

            Task::factory()
              ->count(3)
              ->state(function () use ($column, &$taskPosition) {
                return [
                  "column_id" => $column->id,
                  "position" => $taskPosition++,
                ];
              })
              ->create();
          });
      });

  }
}
