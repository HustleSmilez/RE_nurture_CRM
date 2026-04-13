<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'mobile' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => 'Tampa',
            'state' => 'FL',
            'zip_code' => $this->faker->postcode(),
            'country' => 'USA',
            'company' => $this->faker->company(),
            'title' => $this->faker->jobTitle(),
            'source' => $this->faker->randomElement(['website', 'facebook', 'referral', 'zillow', 'import']),
            'notes' => $this->faker->sentence(),
            'tags' => $this->faker->randomElements(['buyer', 'seller', 'investor', 'leads', 'qualified'], 2),
            'imported_at' => now(),
        ];
    }
}
