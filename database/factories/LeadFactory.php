<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Pipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'pipeline_id' => Pipeline::factory(),
            'status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'proposal', 'negotiation']),
            'value' => $this->faker->randomFloat(2, 100000, 1000000),
            'property_interest' => 'Tampa Bay Area',
            'estimated_close_date' => $this->faker->dateTimeBetween('+10 days', '+90 days'),
            'source' => $this->faker->randomElement(['website', 'facebook', 'referral', 'zillow']),
            'notes' => $this->faker->sentence(),
        ];
    }
}
