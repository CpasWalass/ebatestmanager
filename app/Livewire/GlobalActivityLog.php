<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class GlobalActivityLog extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::with('causer')->latest();

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('causer', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->typeFilter) {
            $query->where('subject_type', 'like', '%' . $this->typeFilter . '%');
        }

        return view('livewire.global-activity-log', [
            'activities' => $query->paginate(20)
        ])->layout('layouts.app');
    }
}
