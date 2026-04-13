<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        Pipeline::createMany([
            [
                'name' => 'Buyer Pipeline',
                'description' => 'Primary pipeline for buyer leads',
                'is_active' => true,
            ],
            [
                'name' => 'Seller Pipeline',
                'description' => 'Pipeline for seller leads and list prospects',
                'is_active' => true,
            ],
            [
                'name' => 'Investor Pipeline',
                'description' => 'Investment property pipeline',
                'is_active' => true,
            ],
            [
                'name' => 'Referral Pipeline',
                'description' => 'Referral source leads',
                'is_active' => true,
            ],
        ]);
    }
}
