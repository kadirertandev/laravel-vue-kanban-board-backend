<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Board extends Model
{
  use HasFactory;

  protected $fillable = [
    "user_id",
    "title",
    "description"
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function columns()
  {
    return $this->hasMany(Column::class)->orderBy("position", "asc");
  }

  public function scopeWithConditionals($query, Request $request)
  {
    $query->when($request->boolean("withColumns"), function ($query) use ($request) {
      $query->with([
        "columns" => fn($q) => $q->withConditionals($request)
      ]);
    });
  }
}
