<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = Contact::all();

        $titles = [
            'Follow up on inquiry',
            'Schedule property showing',
            'Send buyer qualification form',
            'Prepare CMA analysis',
            'Send contract',
            'Request pre-approval letter',
            'Schedule home inspection',
        ];

        foreach ($contacts->take(40) as $contact) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                Task::create([
                    'contact_id' => $contact->id,
                    'title' => $titles[array_rand($titles)],
                    'status' => ['pending', 'in_progress', 'completed'][array_rand([0, 1, 2])],
                    'priority' => ['low', 'medium', 'high', 'urgent'][array_rand([0, 1, 2, 3])],
                    'due_date' => now()->addDays(rand(1, 30)),
                    'description' => 'Task for ' . $contact->full_name,
                ]);
            }
        }
    }
}
