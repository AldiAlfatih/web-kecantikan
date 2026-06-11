<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'skin_type')) {
                $table->string('skin_type')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'skin_tone')) {
                $table->string('skin_tone')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'undertone')) {
                $table->string('undertone')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'vein_color')) {
                $table->string('vein_color')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'skin_problem')) {
                $table->text('skin_problem')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // gak usah diisi, aman
    }
};
