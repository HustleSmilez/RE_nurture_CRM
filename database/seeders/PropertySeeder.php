<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $properties = [
            [
                'address' => '123 Oak Street',
                'city' => 'Tampa',
                'state' => 'FL',
                'zip_code' => '33609',
                'property_type' => 'single_family',
                'bedrooms' => 4,
                'bathrooms' => 2.5,
                'square_feet' => 3200,
                'year_built' => 2015,
                'price' => 450000,
                'status' => 'active',
            ],
            [
                'address' => '456 Maple Avenue',
                'city' => 'Clearwater',
                'state' => 'FL',
                'zip_code' => '33755',
                'property_type' => 'condo',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'square_feet' => 1400,
                'year_built' => 2010,
                'price' => 275000,
                'status' => 'active',
            ],
            [
                'address' => '789 Pine Road',
                'city' => 'St. Petersburg',
                'state' => 'FL',
                'zip_code' => '33701',
                'property_type' => 'townhouse',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'square_feet' => 1800,
                'year_built' => 2018,
                'price' => 325000,
                'status' => 'pending',
            ],
        ];

        Property::insert($properties);
    }
}
