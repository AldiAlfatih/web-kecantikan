<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('user_pca_profiles', function (Blueprint $table) {
      $table->id();

      $table->foreignId('user_id')->constrained()->onDelete('cascade');

      // input user (inti)
      $table->unsignedTinyInteger('skin_tone_level'); // 1-6
      $table->enum('undertone', ['cool','neutral','warm']);
      $table->enum('vein_color', ['blue_purple','green_olive','mixed']);

      // optional input (kalau mau)
      $table->enum('eye_contrast', ['bright','muted'])->nullable(); // atau eye_color
      $table->string('hair_color')->nullable();

      // hasil PCA (output service)
      $table->enum('hue', ['cool','neutral','warm'])->nullable();
      $table->enum('value', ['light','medium','dark'])->nullable();
      $table->enum('chroma', ['bright','muted'])->nullable();
      $table->string('season')->nullable(); // "Light Spring" dll
      $table->enum('confidence', ['low','medium','high'])->nullable();

      $table->timestamps();

      $table->index(['user_id']);
      $table->index(['skin_tone_level','undertone','vein_color']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('user_pca_profiles');
  }
};
