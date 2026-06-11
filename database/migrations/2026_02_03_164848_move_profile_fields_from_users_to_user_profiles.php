<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // pindahkan data lama dari users ke user_profiles
        $users = DB::table('users')->get();

        foreach ($users as $u) {
            DB::table('user_profiles')->updateOrInsert(
                ['user_id' => $u->id],
                [
                    'skin_type' => $u->skin_type ?? null,
                    'tone' => $u->skin_tone ?? null,
                    'undertone' => $u->undertone ?? null,
                    'skin_problem' => $u->skin_problem ?? null,
                    'vein_color' => $u->vein_color ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // tidak rollback data
    }
};
