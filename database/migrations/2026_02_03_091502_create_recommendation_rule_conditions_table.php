<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('recommendation_rule_conditions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('rule_id')
        ->constrained('recommendation_rules')
        ->onDelete('cascade');

      // contoh field: hue, value, undertone, vein_color, skin_tone_level, chroma
      $table->string('field');
      // =, in, !=, between
      $table->string('operator', 20)->default('=');

      // simpan value sebagai JSON biar fleksibel: ["warm","neutral"] atau "warm"
      $table->json('value');

      // AND/OR untuk join antar kondisi
      $table->enum('logic', ['AND','OR'])->default('AND');

      $table->timestamps();

      $table->index(['rule_id','field']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('recommendation_rule_conditions');
  }
};
