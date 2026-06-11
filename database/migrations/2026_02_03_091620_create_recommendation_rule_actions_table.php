<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('recommendation_rule_actions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('rule_id')
        ->constrained('recommendation_rules')
        ->onDelete('cascade');

      // target filter: shade_undertone, shade_tone, product_category
      $table->string('target');
      $table->string('operator', 20)->default('in');
      $table->json('value');

      $table->timestamps();

      $table->index(['rule_id','target']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('recommendation_rule_actions');
  }
};
