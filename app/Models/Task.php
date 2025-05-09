<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
  use HasFactory;

  const POSITION_GAP = 60000;
  const POSITION_MIN = 0.00002;

  protected $fillable = [
    "column_id",
    "description",
    "position"
  ];

  public static function booted()
  {
    static::creating(function ($model) {
      $model->position = self::query()->where('column_id', $model->column_id)->orderByDesc('position')->first()?->position + self::POSITION_GAP;
    });

    static::saved(function ($model) {
      if ($model->position < self::POSITION_MIN) {
        DB::statement("SET @previousPosition := 0");
        DB::statement("
              UPDATE tasks
              SET position = (@previousPosition := @previousPosition + ?)
              WHERE column_id = ?
              ORDER BY position
          ",
          [
            self::POSITION_GAP,
            $model->column_id
          ]
        );
      }
    });
  }

  public function column()
  {
    return $this->belongsTo(Column::class);
  }
}
