<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Column;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ColumnPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user, Board $board): bool
  {
    return $user->is($board->user);
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Column $column): bool
  {
    return $user->is($column->board()->first()->user);
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user, Board $board): bool
  {
    return $user->is($board->user);
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Column $column): bool
  {
    return $user->is($column->board()->first()->user);
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Column $column): bool
  {
    return $user->is($column->board()->first()->user);
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Column $column): bool
  {
    return false;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Column $column): bool
  {
    return false;
  }
}
