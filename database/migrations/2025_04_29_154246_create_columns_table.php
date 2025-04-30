<?php

use App\Models\Board;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('columns', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Board::class)->constrained()->cascadeOnDelete();
      $table->string("title");
      $table->double('position');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('columns');
  }
};
