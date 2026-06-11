<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserDummySeeder::class);
        $this->call(ProductAndShadeSeeder::class);
        $this->call(OrderDummySeeder::class);
    }
    
}
