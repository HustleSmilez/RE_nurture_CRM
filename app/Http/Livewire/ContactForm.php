<?php

namespace App\Http\Livewire;

use App\Models\Contact;
use Livewire\Component;

class ContactForm extends Component
{
    public ?Contact $contact = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $mobile = '';
    public string $company = '';
    public string $city = '';
    public string $state = '';
    public string $zip_code = '';
    public array $tags = [];

    public function mount(Contact $contact = null)
    {
        if ($contact) {
            $this->contact = $contact;
            $this->fillForm();
        }
    }

    public function fillForm()
    {
        $this->first_name = $this->contact->first_name;
        $this->last_name = $this->contact->last_name;
        $this->email = $this->contact->email;
        $this->phone = $this->contact->phone;
        $this->mobile = $this->contact->mobile;
        $this->company = $this->contact->company;
        $this->city = $this->contact->city;
        $this->state = $this->contact->state;
        $this->zip_code = $this->contact->zip_code;
        $this->tags = $this->contact->tags ?? [];
    }

    public function save()
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:contacts,email,' . ($this->contact?->id ?? 'NULL'),
            'phone' => 'nullable|string',
            'mobile' => 'nullable|string',
            'company' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        if ($this->contact) {
            $this->contact->update($validated);
            $message = 'Contact updated successfully';
        } else {
            Contact::create($validated);
            $message = 'Contact created successfully';
        }

        $this->dispatch('contact-saved');
        session()->flash('success', $message);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
