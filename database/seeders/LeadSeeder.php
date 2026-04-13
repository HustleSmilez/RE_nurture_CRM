<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();
        $pipelines = Pipeline::all();

        $statuses = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'closed', 'lost'];

        foreach ($contacts->take(30) as $contact) {
            Lead::create([
                'contact_id' => $contact->id,
                'pipeline_id' => $pipelines->random()->id,
                'status' => $statuses[array_rand($statuses)],
                'value' => rand(100000, 1000000),
                'property_interest' => 'Tampa Bay Area',
                'estimated_close_date' => now()->addDays(rand(10, 90)),
                'source' => ['website', 'facebook', 'referral', 'zillow'][array_rand([0, 1, 2, 3])],
            ]);
        }
    }
}
