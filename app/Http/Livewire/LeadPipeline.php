<?php

namespace App\Http\Livewire;

use App\Models\Lead;
use App\Models\Pipeline;
use Livewire\Component;
use Livewire\WithPagination;

class LeadPipeline extends Component
{
    use WithPagination;

    public ?Pipeline $pipeline = null;
    public string $selectedStatus = '';
    public string $sortBy = 'estimated_close_date';

    public function mount(Pipeline $pipeline = null)
    {
        if ($pipeline) {
            $this->pipeline = $pipeline;
        }
    }

    public function getLeadsProperty()
    {
        $query = $this->pipeline ? $this->pipeline->leads() : Lead::query();

        if ($this->selectedStatus) {
            $query = $query->where('status', $this->selectedStatus);
        }

        return $query->orderBy($this->sortBy, 'asc')->paginate(15);
    }

    public function advanceLead(Lead $lead)
    {
        if ($lead->advance()) {
            $this->dispatch('lead-advanced');
            session()->flash('success', "Lead advanced to {$lead->status}");
        }
    }

    public function closeLead(Lead $lead)
    {
        $lead->markAsClosed();
        $this->dispatch('lead-closed');
        session()->flash('success', 'Lead marked as closed');
    }

    public function loseLead(Lead $lead)
    {
        $lead->markAsLost('Manually marked as lost');
        $this->dispatch('lead-lost');
        session()->flash('success', 'Lead marked as lost');
    }

    public function render()
    {
        return view('livewire.lead-pipeline', [
            'leads' => $this->leads,
            'stats' => $this->pipeline?->getStats(),
        ]);
    }
}
