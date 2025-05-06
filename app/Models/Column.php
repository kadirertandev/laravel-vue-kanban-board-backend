<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Column extends Model
{
  use HasFactory;

  const POSITION_GAP = 60000;
  const POSITION_MIN = 0.00002;

  protected $fillable = [
    "board_id",
    "title",
    "position"
  ];

  public static function booted()
  {
    static::creating(function ($model) {
      $model->position = self::query()->where('board_id', $model->board_id)->orderByDesc('position')->first()?->position + self::POSITION_GAP;
    });

    static::saved(function ($model) {
      if ($model->position < self::POSITION_MIN) {
        DB::statement("SET @previousPosition := 0");
        DB::statement("
              UPDATE columns
              SET position = (@previousPosition := @previousPosition + ?)
              WHERE board_id = ?
              ORDER BY position
          ",
          [
            self::POSITION_GAP,
            $model->board_id
          ]
        );
      }
    });
  }

  public function board()
  {
    return $this->belongsTo(Board::class);
  }

  public function tasks()
  {
    return $this->hasMany(Task::class)->orderBy("position", "desc");
  }

  public function scopeWithConditionals($query, Request $request)
  {
    $query
      ->when($request->boolean("withColumnTasks"), function ($query) {
        $query->with([
          "tasks" => fn($q) => $q->with("column")
        ]);
      })
      ->when($request->boolean("withColumnBoard"), function ($query) {
        $query->with(["board"]);
      });
  }
}
