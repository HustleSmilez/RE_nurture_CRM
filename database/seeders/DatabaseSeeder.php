<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PipelineSeeder::class,
            ContactSeeder::class,
            LeadSeeder::class,
            TaskSeeder::class,
            PropertySeeder::class,
        ]);
    }
}
