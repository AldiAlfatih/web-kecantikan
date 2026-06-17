<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('province')->nullable()->after('phone');
            $table->string('province_id')->nullable()->after('province');
            $table->string('city')->nullable()->after('province_id');
            $table->string('city_id')->nullable()->after('city');
            $table->string('district')->nullable()->after('city_id');
            $table->string('district_id')->nullable()->after('district');
            $table->string('village')->nullable()->after('district_id');
            $table->string('village_id')->nullable()->after('village');
            $table->string('postal_code')->nullable()->after('village_id');
            $table->text('address_detail')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'province',
                'province_id',
                'city',
                'city_id',
                'district',
                'district_id',
                'village',
                'village_id',
                'postal_code',
                'address_detail',
            ]);
        });
    }
};
